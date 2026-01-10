@extends('layouts.legal')

@section('title', 'Terms of Service - Chinese Learner')

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
                <h1 class="legal-title">Terms of Service</h1>
                <p class="legal-date">Effective Date: January 9, 2026</p>
                <p class="legal-intro">
                    Welcome to Chinese Learner! These Terms of Service ("Terms") govern your use of the Chinese Learner website 
                    and mobile application (collectively, "Chinese Learner" or the "Platform"). By accessing or using Chinese Learner, 
                    or by creating an account, you agree to be bound by these Terms. If you do not agree with any part of these Terms, 
                    please refrain from using Chinese Learner.
                </p>
            </div>

            <div class="legal-content">
                <section class="legal-section">
                    <h2>1. Definitions</h2>
                    <p>To ensure clarity, the following terms will have the meanings specified below:</p>
                    <ul>
                        <li><strong>"Chinese Learner," "we," "us," "our"</strong> refers to Chinese Learner, the company operating the Chinese Learner platform.</li>
                        <li><strong>"You," "your," "User"</strong> means any individual or entity using Chinese Learner.</li>
                        <li><strong>"Content"</strong> includes all materials available on Chinese Learner, such as lessons, quizzes, videos, audio files, images, and any user-submitted content.</li>
                        <li><strong>"Subscription"</strong> refers to a paid membership plan that grants access to additional features or premium content.</li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>2. Eligibility</h2>
                    <p>
                        Chinese Learner is designed for users of all ages. By using the Platform, you confirm that you have the legal 
                        capacity to enter into these Terms. If you are a minor in your jurisdiction, you must obtain permission from 
                        a parent or legal guardian before using Chinese Learner.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>3. Account & Security</h2>
                    <p>To maintain a secure environment for all users, you agree to:</p>
                    <ul>
                        <li>Provide accurate and complete information when registering an account.</li>
                        <li>Keep your login credentials confidential and do not share them with others.</li>
                        <li>Notify us immediately at support@chineselearner.com if you suspect unauthorized access to your account.</li>
                    </ul>
                    <p>We reserve the right to suspend or terminate inactive, fraudulent, or suspicious accounts without prior notice.</p>
                </section>

                <section class="legal-section">
                    <h2>4. User Responsibilities</h2>
                    <p>
                        Users are responsible for their own learning progress and comprehension of the language materials provided. 
                        Chinese Learner does not guarantee specific results or language proficiency improvements.
                    </p>
                    <p>Users must not engage in any activities that may disrupt the app's functionality or violate these Terms of Service. 
                        Any abusive, offensive, or inappropriate behavior towards other users or app content is strictly prohibited.</p>
                </section>

                <section class="legal-section">
                    <h2>5. User Content</h2>
                    <ul>
                        <li>You retain ownership of any content you upload to Chinese Learner, but grant us a license to use it for Platform operations.</li>
                        <li>Do not upload content that infringes on others' rights or violates laws.</li>
                        <li>We reserve the right to remove any content that breaches our policies.</li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>6. Privacy & Cookies</h2>
                    <ul>
                        <li>Our <a href="{{ route('client.privacy') }}">Privacy Policy</a> details how we collect, use, and protect your data.</li>
                        <li>We use cookies to enhance your experience. You can manage cookie preferences in your browser settings.</li>
                        <li>Depending on your location, you may have rights under GDPR, CCPA, or other privacy laws, including:
                            <ul>
                                <li>Accessing, correcting, or deleting your data.</li>
                                <li>Requesting data portability.</li>
                            </ul>
                        </li>
                        <li>To exercise these rights, contact us at support@chineselearner.com.</li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>7. Intellectual Property</h2>
                    <p>
                        All the language learning materials, lessons, quizzes, and exercises provided within the app are owned and 
                        created by Chinese Learner. Users are not permitted to reproduce, distribute, or modify the app's content 
                        without explicit permission.
                    </p>
                    <p>
                        Chinese Learner and its logo are trademarks of our company and may not be used without permission. Users retain 
                        their intellectual property rights for content they create within the app.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>8. Indemnity</h2>
                    <p>You agree to indemnify and hold harmless Chinese Learner from any claims, damages, or expenses (including legal fees) arising from:</p>
                    <ul>
                        <li>Your use of the Platform.</li>
                        <li>Your breach of these Terms.</li>
                        <li>Any violation of third-party rights.</li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>9. Limitation of Liability</h2>
                    <ul>
                        <li>We are not liable for indirect, incidental, or consequential damages.</li>
                        <li>Our total liability is limited to the amount you paid in the last 12 months.</li>
                        <li>We are not responsible for disruptions caused by events beyond our control (e.g., natural disasters, wars, internet outages).</li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>10. Termination</h2>
                    <p>
                        Chinese Learner reserves the right to terminate user accounts for violation of these Terms of Service or any 
                        other reasons deemed appropriate. Upon termination, users will lose access to their accounts and any associated data.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>11. Modifications to the Terms</h2>
                    <p>
                        We may update these Terms periodically. While we won't notify users individually, we encourage you to review 
                        this page regularly. Continued use of Chinese Learner after changes constitutes acceptance of the revised Terms.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>12. Governing Law</h2>
                    <p>
                        These Terms of Service shall be governed by the laws of your country, without regard to its conflict of laws principles.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>13. Contact Us</h2>
                    <p>If you have any questions or concerns about these Terms of Service, please contact us:</p>
                    <ul>
                        <li>By email: <a href="mailto:support@chineselearner.com">support@chineselearner.com</a></li>
                    </ul>
                    <p class="legal-thank">Thank you for choosing Chinese Learner as your language learning companion!</p>
                </section>
            </div>
        </div>
    </main>

    @include('client.components.legal-footer')
</div>
@endsection
