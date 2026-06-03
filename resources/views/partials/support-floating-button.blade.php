<!-- Support floating button -->
<style>
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    .support-fab {
        position: fixed;
        bottom: 24px;
        z-index: 1050;
        {{ app()->getLocale() == 'ar' ? 'left: 20px;' : 'right: 20px;' }}
    }

    .support-fab .fab-btn {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #0271ff;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 18px rgba(2, 113, 255, 0.4);
        cursor: pointer;
        border: none;
        outline: none;
        transition: all 0.3s ease;
    }

    .support-fab .fab-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 24px rgba(2, 113, 255, 0.6);
    }

    .support-fab .fab-btn.active {
        animation: pulse 2s infinite;
    }

    .support-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 1049;
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .support-overlay.show {
        display: block;
    }

    .support-panel {
        position: fixed;
        bottom: 90px;
        width: 420px;
        max-width: calc(100% - 40px);
        max-height: calc(100vh - 140px);
        z-index: 1050;
        display: none;
        {{ app()->getLocale() == 'ar' ? 'left: 20px;' : 'right: 20px;' }}
    }

    .support-panel.show {
        display: block;
        animation: slideUp 0.4s ease;
    }

    .support-panel .card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        border: none;
        background: #0271ff;
    }

    .support-panel .card-header {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        padding: 15px 20px;
        backdrop-filter: blur(10px);
    }

    .support-panel .card-header strong {
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .support-panel .card-header .btn-close {
        color: #fff;
        font-size: 1.5rem;
        background: transparent;
        border: none;
        opacity: 0.9;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .support-panel .card-header .btn-close:hover {
        background: rgba(255, 255, 255, 0.2);
        opacity: 1;
    }

    .support-panel .card-body {
        background: #fff;
        padding: 20px;
        max-height: calc(100vh - 240px);
        overflow-y: auto;
    }

    .support-panel .ticket-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .support-panel .ticket-item {
        padding: 0;
        margin-bottom: 12px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        background: #fff;
    }

    .support-panel .ticket-item:hover {
        box-shadow: 0 4px 12px rgba(2, 113, 255, 0.15);
        transform: translateY(-2px);
    }

    .support-panel .ticket-item:last-child {
        margin-bottom: 0;
    }

    .support-panel .ticket-link {
        display: block;
        padding: 14px;
        text-decoration: none;
        color: inherit;
    }

    .support-panel .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .support-panel .ticket-id {
        font-weight: 600;
        color: #0271ff;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
    }

    .support-panel .ticket-id i {
        margin-right: 4px;
        font-size: 0.75rem;
    }

    .support-panel .ticket-status {
        font-size: 0.72rem;
        padding: 4px 10px;
        border-radius: 12px;
        background: #f0f0f0;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .support-panel .ticket-status.new {
        background: #e3f2fd;
        color: #1976d2;
    }

    .support-panel .ticket-status.in-progress {
        background: #fff3e0;
        color: #f57c00;
    }

    .support-panel .ticket-status.closed {
        background: #e8f5e9;
        color: #388e3c;
    }

    .support-panel .ticket-subject {
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
        font-size: 0.95rem;
        line-height: 1.3;
    }

    .support-panel .ticket-description {
        font-size: 0.82rem;
        color: #777;
        margin-bottom: 8px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .support-panel .ticket-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .support-panel .ticket-category {
        display: inline-flex;
        align-items: center;
        font-size: 0.72rem;
        padding: 3px 10px;
        border-radius: 10px;
        font-weight: 600;
    }

    .support-panel .ticket-category i {
        margin-right: 4px;
        font-size: 0.7rem;
    }

    .support-panel .ticket-priority {
        font-size: 0.72rem;
        color: #666;
        display: inline-flex;
        align-items: center;
        background: #f5f5f5;
        padding: 3px 10px;
        border-radius: 10px;
        font-weight: 600;
    }

    .support-panel .ticket-priority i {
        margin-right: 4px;
        font-size: 0.7rem;
    }

    .support-panel .ticket-external-link {
        font-size: 0.72rem;
        color: #0271ff;
        display: inline-flex;
        align-items: center;
        margin-left: auto;
    }

    .support-panel .ticket-external-link i {
        margin-left: 4px;
        font-size: 0.7rem;
    }

    .support-panel .empty-state {
        text-align: center;
        padding: 50px 20px;
    }

    .support-panel .empty-state-icon {
        font-size: 4.5rem;
        color: #e0e0e0;
        margin-bottom: 20px;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .support-panel .empty-state-title {
        font-size: 1.15rem;
        font-weight: 600;
        color: #555;
        margin-bottom: 10px;
    }

    .support-panel .empty-state-text {
        font-size: 0.9rem;
        color: #999;
        line-height: 1.5;
        margin-bottom: 20px;
    }

    .support-panel .view-toggle {
        display: none;
    }

    .support-panel .view-toggle.active {
        display: block;
    }

    .support-panel .back-btn {
        background: transparent;
        border: none;
        color: #0271ff;
        font-weight: 600;
        cursor: pointer;
        padding: 8px 12px;
        margin: -8px -12px 12px -12px;
        font-size: 0.9rem;
        transition: background 0.2s ease;
        border-radius: 6px;
    }

    .support-panel .back-btn:hover {
        background: rgba(2, 113, 255, 0.1);
    }

    .support-panel .back-btn i {
        margin-right: 6px;
    }

    .support-panel .form-control,
    .support-panel .form-select {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .support-panel .form-control:focus,
    .support-panel .form-select:focus {
        border-color: #0271ff;
        box-shadow: 0 0 0 0.2rem rgba(2, 113, 255, 0.25);
    }

    .support-panel .btn-primary {
        background: #0271ff;
        border: none;
        border-radius: 8px;
        padding: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .support-panel .btn-primary:hover {
        background: #0260dd;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 113, 255, 0.4);
    }

    .support-panel .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .support-panel .btn-create-ticket {
        background: #0271ff;
        border: none;
        border-radius: 8px;
        padding: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white;
        box-shadow: 0 4px 12px rgba(2, 113, 255, 0.3);
    }

    .support-panel .btn-create-ticket:hover {
        background: #0260dd;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(2, 113, 255, 0.4);
    }

    .support-panel .btn-create-ticket i {
        margin-right: 6px;
    }

    .support-panel .ticket-skeleton {
        padding: 14px;
        margin-bottom: 12px;
        border-radius: 8px;
        background: #f5f5f5;
        animation: skeleton-loading 1.5s infinite;
    }

    .support-panel .skeleton-line {
        height: 12px;
        background: #e0e0e0;
        border-radius: 4px;
        margin-bottom: 8px;
    }

    .support-panel .skeleton-line.short {
        width: 40%;
    }

    .support-panel .skeleton-line.medium {
        width: 70%;
    }

    .support-panel .skeleton-line.long {
        width: 100%;
    }

    @keyframes skeleton-loading {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    @media (max-width: 576px) {
        .support-panel {
            width: 95%;
            {{ app()->getLocale() == 'ar' ? 'left: 10px; right: 10px;' : 'right: 10px; left: 10px;' }}
        }
    }
</style>

<div class="support-overlay" id="supportOverlay"></div>
<div class="support-fab">
    <div class="support-panel" id="supportPanel">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <strong id="supportPanelTitle">{{ __('admin.support_service.my_tickets') ?? 'My Tickets' }}</strong>
                    <button type="button" id="closeSupportPanel" class="btn-close">&times;</button>
                </div>
            </div>
            <div class="card-body">
                <!-- Ticket List View -->
                <div id="ticketListView" class="view-toggle active">
                    <div id="ticketListLoader" style="padding: 0;">
                        <!-- Skeleton Loaders -->
                        <div class="ticket-skeleton">
                            <div class="skeleton-line short"></div>
                            <div class="skeleton-line medium"></div>
                            <div class="skeleton-line long"></div>
                        </div>
                        <div class="ticket-skeleton">
                            <div class="skeleton-line short"></div>
                            <div class="skeleton-line medium"></div>
                            <div class="skeleton-line long"></div>
                        </div>
                        <div class="ticket-skeleton">
                            <div class="skeleton-line short"></div>
                            <div class="skeleton-line medium"></div>
                            <div class="skeleton-line long"></div>
                        </div>
                    </div>
                    <div id="ticketListContent" style="display: none;">
                        <ul class="ticket-list" id="ticketList"></ul>
                    </div>
                    <div id="ticketEmptyState" class="empty-state" style="display: none;">
                        <div class="empty-state-icon">
                            <i class="fa fa-ticket"></i>
                        </div>
                        <div class="empty-state-title">{{ __('admin.support_service.no_tickets') ?? 'No Tickets Yet' }}
                        </div>
                        <div class="empty-state-text">
                            {{ __('admin.support_service.no_tickets_desc') ?? 'You haven\'t created any support tickets yet.' }}
                        </div>
                    </div>
                    <div class="d-grid" style="margin-top: 16px;">
                        <button type="button" id="createNewTicketBtn" class="btn btn-create-ticket">
                            <i class="fa fa-plus"></i>
                            {{ __('admin.support_service.create_new_ticket') ?? 'Create New Ticket' }}
                        </button>
                    </div>
                </div>

                <!-- Create Ticket Form View -->
                <div id="createTicketView" class="view-toggle">
                    <button type="button" id="backToListBtn" class="back-btn">
                        <i class="fa fa-arrow-left"></i> {{ __('admin.actions.back') ?? 'Back' }}
                    </button>

                    @guest
                        <!-- Login Required Message for Guests -->
                        <div class="text-center py-4">
                            <div style="font-size: 3.5rem; color: #e0e0e0; margin-bottom: 20px;">
                                <i class="fa fa-lock"></i>
                            </div>
                            <h5 class="mb-3" style="color: #555; font-weight: 600;">
                                {{ __('admin.support_service.login_required') ?? 'Login Required' }}</h5>
                            <p class="text-muted mb-4" style="font-size: 0.9rem; line-height: 1.6;">
                                {{ __('admin.support_service.login_required_desc') ?? 'Please login to your account to create a support ticket and contact our support team.' }}
                            </p>
{{--                            <a href="{{ route('gov.show_login_form') }}" class="btn btn-primary"--}}
{{--                                style="padding: 12px 32px;">--}}
{{--                                <i class="fa fa-sign-in-alt" style="margin-right: 6px;"></i>--}}
{{--                                {{ __('admin.actions.login') ?? 'Login' }}--}}
{{--                            </a>--}}
                        </div>
                    @else
                        <!-- Support Ticket Form -->
                        <form id="supportFabForm">
                            <div class="mb-3">
                                <input type="text" name="name" id="sf_name" class="form-control"
                                    placeholder="{{ __('admin.contacts.name') }}"
                                    value="{{ auth()->check() ? auth()->user()->name : '' }}" @auth readonly @endauth
                                    required>
                            </div>
                            <div class="mb-3">
                                <input type="email" name="email" id="sf_email" class="form-control"
                                    placeholder="{{ __('admin.contacts.email') }}"
                                    value="{{ auth()->check() ? auth()->user()->email : '' }}" @auth readonly @endauth
                                    required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="phone" id="sf_phone" class="form-control"
                                    placeholder="{{ __('admin.contacts.phone') }}"
                                    value="{{ auth()->check() ? auth()->user()->phone ?? '' : '' }}"
                                    @auth readonly @endauth>
                            </div>
                            <div class="mb-3">
                                <select name="priority_id" id="sf_priority" class="form-select">
                                    <option value="">
                                        {{ __('admin.support_service.select_priority') ?? 'Select Priority' }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select name="category_id" id="sf_category" class="form-select">
                                    <option value="">
                                        {{ __('admin.support_service.select_category') ?? 'Select Category' }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="subject" id="sf_subject" class="form-control"
                                    placeholder="{{ __('admin.contacts.subject') }}" required>
                            </div>
                            <div class="mb-3">
                                <textarea name="message" id="sf_message" class="form-control" rows="4"
                                    placeholder="{{ __('admin.contacts.message') }}" required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="supportSubmitBtn">
                                    <span class="submit-text">{{ __('admin.actions.send') ?? 'Send' }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </div>
    {{-- <button class="fab-btn" id="openSupportPanel" aria-label="Support">
        <i class="fa fa-headset"></i>
    </button> --}}
</div>

<script>
    (function() {
        const openBtn = document.getElementById('openSupportPanel');
        const closeBtn = document.getElementById('closeSupportPanel');
        const panel = document.getElementById('supportPanel');
        const overlay = document.getElementById('supportOverlay');
        const form = document.getElementById('supportFabForm');
        const submitBtn = document.getElementById('supportSubmitBtn');
        const prioritySelect = document.getElementById('sf_priority');
        const categorySelect = document.getElementById('sf_category');
        const panelTitle = document.getElementById('supportPanelTitle');
        const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

        // View elements
        const ticketListView = document.getElementById('ticketListView');
        const createTicketView = document.getElementById('createTicketView');
        const createNewTicketBtn = document.getElementById('createNewTicketBtn');
        const backToListBtn = document.getElementById('backToListBtn');
        const ticketListLoader = document.getElementById('ticketListLoader');
        const ticketListContent = document.getElementById('ticketListContent');
        const ticketEmptyState = document.getElementById('ticketEmptyState');
        const ticketList = document.getElementById('ticketList');

        // Determine the correct route prefix based on current URL
        function getRoutePrefix() {
            const path = window.location.pathname;
            if (path.includes('/admin/')) return '/{{ app()->getLocale() }}/admin';
            if (path.includes('/owner/')) return '/{{ app()->getLocale() }}/owner';
            if (path.includes('/gov/')) return '/{{ app()->getLocale() }}/gov';
            if (path.includes('/dalal/')) return '/{{ app()->getLocale() }}/dalal';
            return '/{{ app()->getLocale() }}';
        }

        const routePrefix = getRoutePrefix();

        function showPanel() {
            panel.classList.add('show');
            overlay.classList.add('show');
            openBtn.classList.add('active');
        }

        function hidePanel() {
            panel.classList.remove('show');
            overlay.classList.remove('show');
            openBtn.classList.remove('active');
        }

        function setLoading(isLoading) {
            const spinner = submitBtn.querySelector('.spinner-border');
            const text = submitBtn.querySelector('.submit-text');
            if (isLoading) {
                spinner.classList.remove('d-none');
                text.classList.add('d-none');
                submitBtn.disabled = true;
            } else {
                spinner.classList.add('d-none');
                text.classList.remove('d-none');
                submitBtn.disabled = false;
            }
        }

        function switchToCreateView() {
            ticketListView.classList.remove('active');
            createTicketView.classList.add('active');
            panelTitle.textContent = '{{ __('admin.support_service.create_ticket') ?? 'Create Ticket' }}';
            if (isAuthenticated) {
                loadSupportData();
            }
        }

        function switchToListView() {
            createTicketView.classList.remove('active');
            ticketListView.classList.add('active');
            panelTitle.textContent = '{{ __('admin.support_service.my_tickets') ?? 'My Tickets' }}';
            if (form) {
                form.reset();
            }
        }

        function getUserIdentifiers() {
            const email = document.getElementById('sf_email')?.value || '';
            const phone = document.getElementById('sf_phone')?.value || '';
            return {
                email,
                phone
            };
        }

        function fetchUserTickets() {
            const {
                email,
                phone
            } = getUserIdentifiers();

            if (!email && !phone) {
                showEmptyState();
                return;
            }

            // Show loader
            ticketListLoader.style.display = 'block';
            ticketListContent.style.display = 'none';
            ticketEmptyState.style.display = 'none';

            const params = new URLSearchParams();
            if (email) params.append('email', email);
            if (phone) params.append('mobile_no', phone);

            fetch(routePrefix + '/support/tickets/phone?' + params.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    }
                })
                .then(r => r.json())
                .then(res => {
                    ticketListLoader.style.display = 'none';

                    if (res.status === 1 && res.data && res.data.tickets && res.data.tickets.length > 0) {
                        renderTickets(res.data.tickets);
                        ticketListContent.style.display = 'block';
                    } else {
                        showEmptyState();
                    }
                })
                .catch(err => {
                    console.error('Failed to load tickets:', err);
                    ticketListLoader.style.display = 'none';
                    showEmptyState();
                });
        }

        function showEmptyState() {
            ticketListLoader.style.display = 'none';
            ticketListContent.style.display = 'none';
            ticketEmptyState.style.display = 'block';
        }

        function renderTickets(tickets) {
            ticketList.innerHTML = '';

            tickets.forEach(ticket => {
                const li = document.createElement('li');
                li.className = 'ticket-item';

                const categoryStyle = ticket.color ? `background: ${ticket.color}; color: white;` :
                    'background: #e0e0e0; color: #666;';
                const description = ticket.description ? ticket.description.replace(/<[^>]*>/g, '')
                    .substring(0, 100) : '';

                // Determine status class
                let statusClass = '';
                const statusLower = (ticket.status || '').toLowerCase();
                if (statusLower.includes('new')) {
                    statusClass = 'new';
                } else if (statusLower.includes('progress') || statusLower.includes('open')) {
                    statusClass = 'in-progress';
                } else if (statusLower.includes('closed') || statusLower.includes('resolved')) {
                    statusClass = 'closed';
                }

                const ticketUrl = ticket.ticket_url || '#';
                const hasExternalLink = ticketUrl !== '#';

                li.innerHTML = `
                    <a href="${ticketUrl}" class="ticket-link" ${hasExternalLink ? 'target="_blank" rel="noopener noreferrer"' : ''}>
                        <div class="ticket-header">
                            <span class="ticket-id"><i class="fa fa-ticket"></i>#${ticket.ticket_id}</span>
                            <span class="ticket-status ${statusClass}">${ticket.status || 'N/A'}</span>
                        </div>
                        <div class="ticket-subject">${ticket.subject || 'No Subject'}</div>
                        ${description ? `<div class="ticket-description">${description}${description.length >= 100 ? '...' : ''}</div>` : ''}
                        <div class="ticket-meta">
                            ${ticket.category ? `<span class="ticket-category" style="${categoryStyle}"><i class="fa fa-tag"></i>${ticket.category}</span>` : ''}
                            ${ticket.priority ? `<span class="ticket-priority"><i class="fa fa-flag"></i>${ticket.priority}</span>` : ''}
                            ${hasExternalLink ? '<span class="ticket-external-link">View Details<i class="fa fa-external-link-alt"></i></span>' : ''}
                        </div>
                    </a>
                `;

                ticketList.appendChild(li);
            });
        }

        // Load priorities and categories on first open
        let dataLoaded = false;

        function loadSupportData() {
            if (dataLoaded) return;

            // Load priorities
            fetch(routePrefix + '/support/priorities', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    }
                })
                .then(r => r.json())
                .then(res => {
                    if (res.data && Array.isArray(res.data)) {
                        res.data.forEach(priority => {
                            const option = document.createElement('option');
                            option.value = priority.id;
                            option.textContent = priority.name;
                            prioritySelect.appendChild(option);
                        });
                    }
                })
                .catch(err => console.error('Failed to load priorities:', err));

            // Load categories
            fetch(routePrefix + '/support/categories', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    }
                })
                .then(r => r.json())
                .then(res => {
                    if (res.data && Array.isArray(res.data)) {
                        res.data.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            categorySelect.appendChild(option);
                        });
                    }
                })
                .catch(err => console.error('Failed to load categories:', err));

            dataLoaded = true;
        }

        openBtn && openBtn.addEventListener('click', function() {
            if (panel.classList.contains('show')) {
                hidePanel();
            } else {
                // For authenticated users, show ticket list; for guests, show create form with login prompt
                if (isAuthenticated) {
                    switchToListView();
                    fetchUserTickets();
                } else {
                    switchToCreateView();
                }
                showPanel();
            }
        });

        closeBtn && closeBtn.addEventListener('click', hidePanel);
        overlay && overlay.addEventListener('click', hidePanel);

        createNewTicketBtn && createNewTicketBtn.addEventListener('click', function() {
            switchToCreateView();
        });

        backToListBtn && backToListBtn.addEventListener('click', function() {
            switchToListView();
            fetchUserTickets();
        });

        // Form submission
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                setLoading(true);

                const formData = {
                    name: document.getElementById('sf_name').value,
                    email: document.getElementById('sf_email').value,
                    phone: document.getElementById('sf_phone').value,
                    subject: document.getElementById('sf_subject').value,
                    message: document.getElementById('sf_message').value,
                };

                // Add priority if selected
                const priorityId = document.getElementById('sf_priority').value;
                if (priorityId) {
                    formData.priority_id = parseInt(priorityId);
                }

                // Add category if selected
                const categoryId = document.getElementById('sf_category').value;
                if (categoryId) {
                    formData.category_id = parseInt(categoryId);
                }

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch(routePrefix + '/support/ticket', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token || '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(r => r.json())
                    .then(res => {
                        setLoading(false);
                        if (res.is_success || res.status === 'success') {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __('admin.swal.saved_success') }}',
                                    text: res.message ||
                                        '{{ __('admin.support_service.ticket_created') ?? 'Ticket created successfully' }}',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                alert(res.message ||
                                    '{{ __('admin.support_service.ticket_created') ?? 'Ticket created successfully' }}'
                                    );
                            }
                            form.reset();

                            // Switch back to list view and refresh tickets
                            setTimeout(() => {
                                switchToListView();
                                fetchUserTickets();
                            }, 2100);
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('admin.swal.error') }}',
                                    text: res.message ||
                                        '{{ __('admin.swal.unexpected_error') }}'
                                });
                            } else {
                                alert(res.message || '{{ __('admin.swal.unexpected_error') }}');
                            }
                        }
                    })
                    .catch(err => {
                        setLoading(false);
                        console.error('Error:', err);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('admin.swal.error') }}',
                                text: '{{ __('admin.swal.unexpected_error') }}'
                            });
                        } else {
                            alert('{{ __('admin.swal.unexpected_error') }}');
                        }
                    });
            });
        }
    })();
</script>
