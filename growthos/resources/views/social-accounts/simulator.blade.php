<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meta Login - Simulator</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            color: #1c1e21;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }

        .meta-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 12px 28px 0 rgba(0, 0, 0, 0.2), 0 2px 4px 0 rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 580px;
            overflow: hidden;
        }

        .meta-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e5e5e5;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .meta-header .facebook-logo {
            font-size: 1.8rem;
            color: #1877f2;
        }

        .meta-header .app-info {
            font-size: 0.95rem;
            font-weight: 600;
        }

        .meta-body {
            padding: 1.75rem;
        }

        .meta-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #050505;
        }

        .meta-subtitle {
            font-size: 0.875rem;
            color: #65676b;
            margin-bottom: 1.5rem;
        }

        .permission-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 0.75rem;
            border: 1px solid #e4e6eb;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            transition: background-color 0.2s;
        }

        .permission-item:hover {
            background-color: #f8f9fa;
        }

        .permission-details {
            flex: 1;
        }

        .permission-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #050505;
        }

        .permission-desc {
            font-size: 0.8rem;
            color: #65676b;
        }

        .meta-footer {
            background-color: #f0f2f5;
            padding: 1rem 1.75rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            border-top: 1px solid #e5e5e5;
        }

        .btn-facebook {
            background-color: #1877f2;
            color: #ffffff;
            font-weight: 600;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
        }

        .btn-facebook:hover {
            background-color: #166fe5;
            color: #ffffff;
        }

        .btn-cancel {
            background-color: #e4e6eb;
            color: #4b4f56;
            font-weight: 600;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            text-decoration: none;
            text-align: center;
        }

        .btn-cancel:hover {
            background-color: #d8dadf;
            color: #4b4f56;
        }
    </style>
</head>
<body>

<div class="meta-card">
    <!-- Header -->
    <div class="meta-header">
        <i class="bi bi-facebook facebook-logo"></i>
        <div class="app-info">
            Log in with Facebook
        </div>
        <span class="badge bg-danger ms-auto text-uppercase" style="font-size:0.65rem;letter-spacing:0.5px;">Simulator Mode</span>
    </div>

    <!-- Body -->
    <div class="meta-body">
        <h4 class="meta-title">GrowthOS is requesting access to:</h4>
        <p class="meta-subtitle">Choose the Facebook Pages and Instagram Business accounts you'd like to link to your GrowthOS organization workspace.</p>

        <form id="simulatorForm" action="{{ route('social-accounts.callback') }}" method="GET">
            <!-- OAuth State -->
            <input type="hidden" name="state" value="{{ $state }}">
            <input type="hidden" name="code" value="mock_authorization_code_987">

            <div class="mb-4">
                <label class="form-label fw-bold" style="font-size: 0.85rem; color: #65676b; text-transform: uppercase;">Select Facebook Pages</label>
                
                <div class="permission-item">
                    <div class="form-check d-flex align-items-center w-100 gap-2 m-0 p-0">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="selected_pages[]" value="1001" id="page1001" checked style="width:1.2rem;height:1.2rem;">
                        <label class="form-check-label permission-details" for="page1001">
                            <span class="permission-title d-block">Acme Corp Facebook Page</span>
                            <span class="permission-desc d-block text-muted">Page ID: 1001 · Category: Business/Brand</span>
                        </label>
                    </div>
                </div>

                <div class="permission-item">
                    <div class="form-check d-flex align-items-center w-100 gap-2 m-0 p-0">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="selected_pages[]" value="1002" id="page1002" style="width:1.2rem;height:1.2rem;">
                        <label class="form-check-label permission-details" for="page1002">
                            <span class="permission-title d-block">GrowthOS Demo Page</span>
                            <span class="permission-desc d-block text-muted">Page ID: 1002 · Category: App Showcase</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold" style="font-size: 0.85rem; color: #65676b; text-transform: uppercase;">Select Instagram Business Accounts</label>

                <div class="permission-item">
                    <div class="form-check d-flex align-items-center w-100 gap-2 m-0 p-0">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="selected_instagrams[]" value="5001" id="insta5001" checked style="width:1.2rem;height:1.2rem;">
                        <label class="form-check-label permission-details" for="insta5001">
                            <span class="permission-title d-block">@acme_instagram</span>
                            <span class="permission-desc d-block text-muted">Linked to: Acme Corp Facebook Page</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning py-2.5 px-3" style="font-size:0.75rem;border-radius:6px;border:none;">
                <i class="bi bi-shield-lock-fill me-1"></i>
                By continuing, GrowthOS will receive the permissions configured in services.php, including <strong>pages_show_list, pages_read_engagement, pages_manage_posts, instagram_basic, instagram_content_publish, business_management</strong>.
            </div>

            <!-- hidden strings to pass to callback easily -->
            <input type="hidden" name="pages" id="pagesInput">
            <input type="hidden" name="instagrams" id="instagramsInput">
        </form>
    </div>

    <!-- Footer -->
    <div class="meta-footer">
        <a href="{{ route('social-accounts.callback', ['error' => 'access_denied', 'state' => $state]) }}" class="btn btn-cancel">
            Cancel
        </a>
        <button type="button" class="btn btn-facebook" onclick="submitSimulator()">
            Continue as {{ auth()->user()->name }}
        </button>
    </div>
</div>

<script>
    function submitSimulator() {
        const form = document.getElementById('simulatorForm');
        
        // Collect pages
        const pages = [];
        const pageChecks = document.getElementsByName('selected_pages[]');
        for (let i = 0; i < pageChecks.length; i++) {
            if (pageChecks[i].checked) {
                pages.push(pageChecks[i].value);
            }
        }
        document.getElementById('pagesInput').value = pages.join(',');

        // Collect instagrams
        const instagrams = [];
        const instaChecks = document.getElementsByName('selected_instagrams[]');
        for (let i = 0; i < instaChecks.length; i++) {
            if (instaChecks[i].checked) {
                instagrams.push(instaChecks[i].value);
            }
        }
        document.getElementById('instagramsInput').value = instagrams.join(',');

        form.submit();
    }
</script>
</body>
</html>
