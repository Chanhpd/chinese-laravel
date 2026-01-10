@extends('layouts.legal')

@section('title', 'Privacy Policy - Chinese Learner')

@push('styles')
<link rel="stylesheet" href="{{ asset('client-assets/css/variables.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/base.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/layout.css') }}">
<link rel="stylesheet" href="{{ asset('client-assets/css/legal.css') }}">
@endpush

@section('content')
<div class="client-container">
    @include('client.components.legal-header')

    <main class="legal-main">
        <div class="legal-container">
            <div class="legal-header">
                <h1 class="legal-title">Privacy Policy</h1>
                <p class="legal-date">Effective Date: January 9, 2026</p>
                <p class="legal-intro">
                    At Chinese Learner, we are committed to protecting your privacy and ensuring the security of your personal 
                    information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when 
                    you use our website and mobile application.
                </p>
            </div>

            <div class="legal-content">
                <section class="legal-section">
                    <h2>1. Information We Collect</h2>
                    <h3>1.1 Personal Information</h3>
                    <p>When you create an account or use our services, we may collect:</p>
                    <ul>
                        <li>Name and email address</li>
                        <li>Username and password</li>
                        <li>Profile information (optional)</li>
                        <li>Learning preferences and progress data</li>
                    </ul>

                    <h3>1.2 Usage Information</h3>
                    <p>We automatically collect information about how you use our Platform, including:</p>
                    <ul>
                        <li>Device information (device type, operating system, browser type)</li>
                        <li>IP address and location data</li>
                        <li>Pages visited and features used</li>
                        <li>Time spent on the Platform</li>
                        <li>Learning activity and progress</li>
                    </ul>

                    <h3>1.3 Cookies and Tracking Technologies</h3>
                    <p>
                        We use cookies and similar tracking technologies to enhance your experience, remember your preferences, 
                        and analyze Platform usage. You can manage cookie preferences through your browser settings.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>2. How We Use Your Information</h2>
                    <p>We use the collected information for the following purposes:</p>
                    <ul>
                        <li><strong>Provide Services:</strong> To deliver and maintain our language learning platform and personalize your experience.</li>
                        <li><strong>Improve Platform:</strong> To analyze usage patterns and improve our content, features, and services.</li>
                        <li><strong>Communication:</strong> To send you updates, notifications, and respond to your inquiries.</li>
                        <li><strong>Security:</strong> To detect, prevent, and address technical issues and fraudulent activities.</li>
                        <li><strong>Legal Compliance:</strong> To comply with legal obligations and enforce our Terms of Service.</li>
                        <li><strong>Marketing:</strong> To send promotional materials (you can opt-out at any time).</li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>3. Information Sharing and Disclosure</h2>
                    <p>We do not sell your personal information to third parties. We may share your information in the following circumstances:</p>
                    <ul>
                        <li><strong>Service Providers:</strong> With trusted third-party service providers who assist in operating our Platform (e.g., hosting, analytics, payment processing).</li>
                        <li><strong>Legal Requirements:</strong> When required by law, court order, or governmental request.</li>
                        <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets.</li>
                        <li><strong>With Your Consent:</strong> When you explicitly authorize us to share your information.</li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>4. Data Security</h2>
                    <p>
                        We implement industry-standard security measures to protect your personal information from unauthorized access, 
                        disclosure, alteration, or destruction. These measures include:
                    </p>
                    <ul>
                        <li>Encryption of data in transit and at rest</li>
                        <li>Regular security assessments and updates</li>
                        <li>Access controls and authentication mechanisms</li>
                        <li>Secure data storage practices</li>
                    </ul>
                    <p>
                        However, no method of transmission over the internet or electronic storage is 100% secure. While we strive 
                        to protect your information, we cannot guarantee absolute security.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>5. Your Rights and Choices</h2>
                    <p>Depending on your location, you may have the following rights regarding your personal information:</p>
                    <ul>
                        <li><strong>Access:</strong> Request access to the personal information we hold about you.</li>
                        <li><strong>Correction:</strong> Request correction of inaccurate or incomplete information.</li>
                        <li><strong>Deletion:</strong> Request deletion of your personal information (subject to legal obligations).</li>
                        <li><strong>Data Portability:</strong> Request a copy of your data in a structured, machine-readable format.</li>
                        <li><strong>Opt-Out:</strong> Unsubscribe from marketing communications at any time.</li>
                        <li><strong>Object:</strong> Object to certain processing activities.</li>
                    </ul>
                    <p>To exercise these rights, please contact us at <a href="mailto:support@chineselearner.com">support@chineselearner.com</a>.</p>
                </section>

                <section class="legal-section">
                    <h2>6. Children's Privacy</h2>
                    <p>
                        Chinese Learner is suitable for users of all ages. If you are under 13 years old (or the minimum age in your 
                        jurisdiction), you must obtain parental or guardian consent before using our Platform. We do not knowingly 
                        collect personal information from children without parental consent.
                    </p>
                    <p>
                        If you believe we have collected information from a child without proper consent, please contact us immediately 
                        so we can take appropriate action.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>7. Third-Party Links and Services</h2>
                    <p>
                        Our Platform may contain links to third-party websites or services. We are not responsible for the privacy 
                        practices of these third parties. We encourage you to review their privacy policies before providing any 
                        personal information.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>8. International Data Transfers</h2>
                    <p>
                        Your information may be transferred to and processed in countries other than your country of residence. 
                        We ensure that appropriate safeguards are in place to protect your information in accordance with this 
                        Privacy Policy and applicable laws.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>9. Data Retention</h2>
                    <p>
                        We retain your personal information for as long as necessary to fulfill the purposes outlined in this Privacy 
                        Policy, unless a longer retention period is required by law. When your information is no longer needed, we 
                        will securely delete or anonymize it.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>10. Changes to This Privacy Policy</h2>
                    <p>
                        We may update this Privacy Policy from time to time to reflect changes in our practices or legal requirements. 
                        We will notify you of significant changes by posting the updated policy on this page with a new effective date. 
                        We encourage you to review this Privacy Policy periodically.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>11. Contact Us</h2>
                    <p>If you have any questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us:</p>
                    <ul>
                        <li>By email: <a href="mailto:support@chineselearner.com">support@chineselearner.com</a></li>
                    </ul>
                    <p class="legal-thank">Thank you for trusting Chinese Learner with your language learning journey!</p>
                </section>
            </div>
        </div>
    </main>

    @include('client.components.legal-footer')
</div>
@endsection
