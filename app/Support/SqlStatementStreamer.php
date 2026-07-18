<?php

namespace App\Support;

/**
 * Splits a stream of SQL text into individual executable statements.
 *
 * The reader is fed arbitrarily sized chunks (for example straight from
 * {@see gzread()}) and returns every complete statement once its terminating
 * semicolon is reached. It understands MySQL/MariaDB single-quoted strings,
 * backtick-quoted identifiers, and `--`, `#`, and block comments, so
 * semicolons, quotes, and newlines that appear inside string values or
 * comments never split a statement in the wrong place. State is retained
 * between chunks, so a statement — or even a two-character token like `--` —
 * may straddle a chunk boundary safely.
 */
final class SqlStatementStreamer
{
    private const MODE_DEFAULT = 0;

    private const MODE_STRING = 1;

    private const MODE_IDENTIFIER = 2;

    private const MODE_LINE_COMMENT = 3;

    private const MODE_BLOCK_COMMENT = 4;

    private int $mode = self::MODE_DEFAULT;

    private string $buffer = '';

    private bool $escaped = false;

    /**
     * A single character held back for two-character token look-ahead
     * (`--`, `/*`, and `*&#47;`) so those tokens are recognised even when they
     * span a chunk boundary.
     */
    private ?string $pending = null;

    /**
     * Feed a chunk of SQL and return every complete statement it finishes.
     *
     * @return list<string>
     */
    public function push(string $chunk): array
    {
        $statements = [];
        $length = strlen($chunk);

        for ($i = 0; $i < $length; $i++) {
            $this->feed($chunk[$i], $statements);
        }

        return $statements;
    }

    /**
     * Return the trailing statement, if any, once the stream has ended.
     *
     * @return list<string>
     */
    public function flush(): array
    {
        if ($this->mode === self::MODE_DEFAULT && $this->pending !== null) {
            $this->buffer .= $this->pending;
        }

        $this->pending = null;

        $statement = trim($this->buffer);
        $this->buffer = '';

        return $statement === '' ? [] : [$statement];
    }

    /**
     * @param  list<string>  $statements
     */
    private function feed(string $char, array &$statements): void
    {
        switch ($this->mode) {
            case self::MODE_LINE_COMMENT:
                if ($char === "\n") {
                    $this->mode = self::MODE_DEFAULT;
                }

                return;

            case self::MODE_BLOCK_COMMENT:
                if ($this->pending === '*' && $char === '/') {
                    $this->pending = null;
                    $this->mode = self::MODE_DEFAULT;

                    return;
                }

                $this->pending = $char === '*' ? '*' : null;

                return;

            case self::MODE_STRING:
                $this->buffer .= $char;

                if ($this->escaped) {
                    $this->escaped = false;
                } elseif ($char === '\\') {
                    $this->escaped = true;
                } elseif ($char === "'") {
                    $this->mode = self::MODE_DEFAULT;
                }

                return;

            case self::MODE_IDENTIFIER:
                $this->buffer .= $char;

                if ($char === '`') {
                    $this->mode = self::MODE_DEFAULT;
                }

                return;

            default:
                $this->feedDefault($char, $statements);
        }
    }

    /**
     * @param  list<string>  $statements
     */
    private function feedDefault(string $char, array &$statements): void
    {
        if ($this->pending === '-') {
            $this->pending = null;

            if ($char === '-') {
                $this->mode = self::MODE_LINE_COMMENT;

                return;
            }

            $this->buffer .= '-';
        } elseif ($this->pending === '/') {
            $this->pending = null;

            if ($char === '*') {
                $this->mode = self::MODE_BLOCK_COMMENT;

                return;
            }

            $this->buffer .= '/';
        }

        switch ($char) {
            case '-':
                $this->pending = '-';

                return;

            case '/':
                $this->pending = '/';

                return;

            case '#':
                $this->mode = self::MODE_LINE_COMMENT;

                return;

            case "'":
                $this->buffer .= $char;
                $this->mode = self::MODE_STRING;

                return;

            case '`':
                $this->buffer .= $char;
                $this->mode = self::MODE_IDENTIFIER;

                return;

            case ';':
                $statement = trim($this->buffer);
                $this->buffer = '';

                if ($statement !== '') {
                    $statements[] = $statement;
                }

                return;

            default:
                $this->buffer .= $char;
        }
    }
}
