<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>Student Application - Consent Agreement</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/design-system.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <style>
        .consent-card {
            max-width: 600px;
        }
        .consent-icon {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
            border-radius: var(--radius-2xl);
            margin: 0 auto var(--spacing-6);
        }
        .consent-icon svg {
            width: 40px;
            height: 40px;
            color: var(--color-white);
        }
        .consent-text {
            background-color: var(--color-gray-50);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            padding: var(--spacing-6);
            margin-bottom: var(--spacing-6);
            max-height: 300px;
            overflow-y: auto;
        }
        .consent-text h4 {
            color: var(--color-primary);
            margin-bottom: var(--spacing-4);
            display: flex;
            align-items: center;
            gap: var(--spacing-2);
        }
        .consent-text p {
            font-size: var(--font-size-sm);
            color: var(--color-text-secondary);
            line-height: var(--line-height-relaxed);
            margin-bottom: var(--spacing-4);
        }
        .consent-text ul {
            padding-left: var(--spacing-6);
            margin-bottom: var(--spacing-4);
        }
        .consent-text li {
            font-size: var(--font-size-sm);
            color: var(--color-text-secondary);
            margin-bottom: var(--spacing-2);
        }
        .consent-checkbox {
            background-color: var(--color-info-light);
            border: 1px solid var(--color-accent);
            border-radius: var(--radius-lg);
            padding: var(--spacing-4);
            margin-bottom: var(--spacing-6);
        }
        .consent-checkbox .form-check {
            margin-bottom: 0;
        }
        .consent-checkbox .form-check-label {
            font-weight: var(--font-weight-medium);
            color: var(--color-text-primary);
        }
        .consent-actions {
            display: flex;
            gap: var(--spacing-4);
        }
        .consent-actions .btn {
            flex: 1;
        }
        .school-badge {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-2);
            background-color: var(--color-primary);
            color: var(--color-white);
            padding: var(--spacing-2) var(--spacing-4);
            border-radius: var(--radius-full);
            font-size: var(--font-size-xs);
            font-weight: var(--font-weight-semibold);
            margin-bottom: var(--spacing-4);
        }
    </style>
</head>
<body>
    <div class="auth-layout">
        <div class="auth-container consent-card">
            <div class="auth-card">
                <div class="text-center">
                    <div class="school-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                        Evelio Javier Memorial National High School
                    </div>
                    <div class="consent-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <h1 class="auth-title">Privacy Consent Agreement</h1>
                    <p class="auth-subtitle">Please read and agree to the terms before proceeding with your application</p>
                </div>

                <div class="consent-text">
                    <h4>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                        Data Privacy Notice
                    </h4>
                    <p>In accordance with the Data Privacy Act of 2012 (Republic Act No. 10173), Evelio Javier Memorial National High School is committed to protecting your personal information.</p>
                    
                    <p><strong>Information We Collect:</strong></p>
                    <ul>
                        <li>Personal identification details (name, birthdate, address)</li>
                        <li>Contact information (phone number, email address)</li>
                        <li>Educational background and academic records</li>
                        <li>Guardian and parent information</li>
                    </ul>

                    <p><strong>Purpose of Data Collection:</strong></p>
                    <ul>
                        <li>Student enrollment and registration processing</li>
                        <li>Academic record management and reporting</li>
                        <li>Communication with students and guardians</li>
                        <li>Compliance with DepEd regulations</li>
                    </ul>

                    <p><strong>Data Protection:</strong></p>
                    <p>Your personal data will be stored securely and will only be accessed by authorized school personnel. We implement appropriate security measures to protect your information against unauthorized access, alteration, or disclosure.</p>

                    <p><strong>Your Rights:</strong></p>
                    <p>You have the right to access, correct, and request deletion of your personal data. For any concerns regarding your data, please contact the school administration.</p>
                </div>

                <div class="consent-checkbox">
                    <div class="form-check">
                        <input type="checkbox" id="agreeConsent" class="form-check-input">
                        <label for="agreeConsent" class="form-check-label">
                            I have read and understood the Privacy Policy. I hereby give my consent for the processing of my personal data by authorized personnel for academic management purposes.
                        </label>
                    </div>
                </div>

                <div class="consent-actions">
                    <a href="index.php" class="btn btn-secondary btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"/>
                            <polyline points="12 19 5 12 12 5"/>
                        </svg>
                        Go Back
                    </a>
                    <button type="button" id="proceedBtn" class="btn btn-primary btn-lg" disabled>
                        Proceed
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </div>

                <div class="auth-footer">
                    <p class="text-muted text-sm mt-6">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 4px;">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        Your data is protected under the Data Privacy Act of 2012
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('agreeConsent');
            const proceedBtn = document.getElementById('proceedBtn');

            checkbox.addEventListener('change', function() {
                proceedBtn.disabled = !this.checked;
            });

            proceedBtn.addEventListener('click', function() {
                if (checkbox.checked) {
                    window.location.href = 'apply_register.php';
                }
            });
        });
    </script>
</body>
</html>
