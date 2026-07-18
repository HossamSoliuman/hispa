<?php

namespace Tests\Unit;

use App\Support\SqlStatementStreamer;
use PHPUnit\Framework\TestCase;

class SqlStatementStreamerTest extends TestCase
{
    /**
     * Feed a whole SQL string through the streamer in one push.
     *
     * @return list<string>
     */
    private function split(string $sql): array
    {
        $streamer = new SqlStatementStreamer;

        return array_merge($streamer->push($sql), $streamer->flush());
    }

    /**
     * Feed a SQL string one byte at a time to prove chunk-boundary safety.
     *
     * @return list<string>
     */
    private function splitByChar(string $sql): array
    {
        $streamer = new SqlStatementStreamer;
        $statements = [];

        foreach (str_split($sql) as $char) {
            $statements = array_merge($statements, $streamer->push($char));
        }

        return array_merge($statements, $streamer->flush());
    }

    public function test_splits_simple_statements(): void
    {
        $this->assertSame(
            ['SELECT 1', 'SELECT 2'],
            $this->split('SELECT 1; SELECT 2;')
        );
    }

    public function test_returns_trailing_statement_without_semicolon(): void
    {
        $this->assertSame(['SELECT 1', 'SELECT 2'], $this->split('SELECT 1; SELECT 2'));
    }

    public function test_ignores_empty_statements(): void
    {
        $this->assertSame(['SELECT 1'], $this->split(';;  ; SELECT 1; ;'));
    }

    public function test_semicolon_inside_string_does_not_split(): void
    {
        $this->assertSame(
            ["INSERT INTO t VALUES ('a; b; c')"],
            $this->split("INSERT INTO t VALUES ('a; b; c');")
        );
    }

    public function test_escaped_quote_inside_string_does_not_end_it(): void
    {
        $sql = "INSERT INTO t VALUES ('it\\'s; fine'); SELECT 1;";

        $this->assertSame(
            ["INSERT INTO t VALUES ('it\\'s; fine')", 'SELECT 1'],
            $this->split($sql)
        );
    }

    public function test_escaped_backslash_before_quote_still_ends_string(): void
    {
        $sql = "INSERT INTO t VALUES ('path\\\\'); SELECT 2;";

        $this->assertSame(
            ["INSERT INTO t VALUES ('path\\\\')", 'SELECT 2'],
            $this->split($sql)
        );
    }

    public function test_doubled_quote_inside_string_is_safe(): void
    {
        $this->assertSame(
            ["INSERT INTO t VALUES ('it''s; ok')"],
            $this->split("INSERT INTO t VALUES ('it''s; ok');")
        );
    }

    public function test_semicolon_inside_backtick_identifier_does_not_split(): void
    {
        $this->assertSame(
            ['CREATE TABLE `we;ird` (x int)', 'SELECT 1'],
            $this->split('CREATE TABLE `we;ird` (x int); SELECT 1;')
        );
    }

    public function test_line_comment_with_semicolon_is_skipped(): void
    {
        $sql = "-- a comment; with a semicolon\nSELECT 1;";

        $this->assertSame(['SELECT 1'], $this->split($sql));
    }

    public function test_hash_comment_is_skipped(): void
    {
        $sql = "# hash; comment\nSELECT 1;";

        $this->assertSame(['SELECT 1'], $this->split($sql));
    }

    public function test_block_comment_with_semicolon_is_skipped(): void
    {
        $this->assertSame(
            ['SELECT 1'],
            $this->split('/* block; comment */ SELECT 1;')
        );
    }

    public function test_metadata_json_comment_is_skipped(): void
    {
        $sql = '-- HISPA-BACKUP-META {"tables":{"users":3},"note":"a; b"}'."\nSELECT 1;";

        $this->assertSame(['SELECT 1'], $this->split($sql));
    }

    public function test_newline_inside_string_value_is_preserved_and_not_split(): void
    {
        $sql = "INSERT INTO t VALUES ('line1\nline2');\nSELECT 1;";

        $this->assertSame(
            ["INSERT INTO t VALUES ('line1\nline2')", 'SELECT 1'],
            $this->split($sql)
        );
    }

    public function test_multi_row_insert_across_lines(): void
    {
        $sql = "INSERT INTO t (a) VALUES\n(1),\n(2),\n(3);";

        $this->assertSame(
            ["INSERT INTO t (a) VALUES\n(1),\n(2),\n(3)"],
            $this->split($sql)
        );
    }

    public function test_char_by_char_matches_whole_string(): void
    {
        $sql = <<<'SQL'
        -- HISPA-BACKUP-META {"tables":{"users":1}}
        SET FOREIGN_KEY_CHECKS = 0;
        DROP TABLE IF EXISTS `users`;
        CREATE TABLE `users` (`id` int, `bio` text COMMENT 'has ; and -- and `ticks`');
        INSERT INTO `users` (`id`, `bio`) VALUES
        (1, 'a; b'),
        (2, 'it\'s a\ntest');
        SET FOREIGN_KEY_CHECKS = 1;
        SQL;

        $this->assertSame($this->split($sql), $this->splitByChar($sql));
    }
}
