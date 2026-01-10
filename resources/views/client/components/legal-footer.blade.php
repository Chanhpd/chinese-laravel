{{-- Legal Footer Component (Minimal) --}}
<footer class="legal-footer">
    <div class="legal-footer-container">
        <div class="footer-brand-section">
            <h3>Chinese Learner</h3>
            <p>A comprehensive platform that makes your Chinese learning process faster and easier.</p>
        </div>
        
        <div class="footer-links-section">
            <div class="footer-column">
                <h4>Legal</h4>
                <ul>
                    <li><a href="{{ route('client.terms') }}">Terms of Service</a></li>
                    <li><a href="{{ route('client.privacy') }}">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="{{ route('client.index') }}">Home</a></li>
                    @auth
                        <li><a href="{{ route('client.home') }}">Dashboard</a></li>
                    @else
                        <li><a href="{{ route('client.login') }}">Login</a></li>
                        <li><a href="{{ route('client.register') }}">Sign Up</a></li>
                    @endauth
                </ul>
            </div>
            <div class="footer-column">
                <h4>Contact</h4>
                <ul>
                    <li><a href="mailto:support@chineselearner.com">support@chineselearner.com</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>Copyright © {{ date('Y') }} Chinese Learner. All Rights Reserved.</p>
    </div>
</footer>

<style>
    .legal-footer {
        background: #f8fafb;
        border-top: 1px solid #e5e7eb;
        margin-top: 80px;
    }

    .legal-footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 60px 40px 40px;
        display: grid;
        grid-template-columns: 2fr 3fr;
        gap: 60px;
    }

    .footer-brand-section h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 16px 0;
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .footer-brand-section p {
        font-size: 0.9375rem;
        color: #6b7280;
        line-height: 1.6;
        margin: 0;
    }

    .footer-links-section {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 40px;
    }

    .footer-column h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0 0 16px 0;
    }

    .footer-column ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-column li {
        margin-bottom: 12px;
    }

    .footer-column a {
        color: #6b7280;
        text-decoration: none;
        font-size: 0.9375rem;
        transition: color 0.2s ease;
    }

    .footer-column a:hover {
        color: #2563eb;
    }

    .footer-bottom {
        text-align: center;
        padding: 24px 40px;
        border-top: 1px solid #e5e7eb;
    }

    .footer-bottom p {
        margin: 0;
        font-size: 0.875rem;
        color: #9ca3af;
    }

    @media (max-width: 768px) {
        .legal-footer-container {
            grid-template-columns: 1fr;
            gap: 40px;
            padding: 40px 24px 32px;
        }

        .footer-links-section {
            grid-template-columns: 1fr;
            gap: 32px;
        }

        .footer-bottom {
            padding: 20px 24px;
        }
    }
</style>
