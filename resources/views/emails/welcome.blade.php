<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to DesiredWash</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .wrapper { max-width: 560px; margin: 0 auto; }
        .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        .header { background: linear-gradient(135deg, #FF6B6B, #FFB347); padding: 36px 32px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 28px; font-weight: 800; }
        .header p { color: rgba(255,255,255,.85); margin: 8px 0 0; font-size: 15px; }
        .body { padding: 32px; }
        .body h2 { color: #1a1a2e; font-size: 20px; margin: 0 0 12px; }
        .body p { color: #555; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }
        .cta { display: inline-block; background: #FF6B6B; color: #fff !important; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-weight: 700; font-size: 15px; margin: 8px 0 24px; }
        .steps { background: #fafafa; border-radius: 12px; padding: 20px 24px; margin: 20px 0; }
        .step { display: flex; align-items: flex-start; margin-bottom: 14px; }
        .step:last-child { margin-bottom: 0; }
        .step-icon { font-size: 22px; margin-right: 14px; flex-shrink: 0; }
        .step-text strong { display: block; color: #1a1a2e; font-size: 14px; }
        .step-text span { color: #777; font-size: 13px; }
        .footer { text-align: center; padding: 20px 32px; color: #aaa; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>🧺 Welcome to DesiredWash!</h1>
                <p>Your laundry, handled with care</p>
            </div>
            <div class="body">
                <h2>Hi {{ $user->name }}! 👋</h2>
                <p>Your account has been created successfully. We're excited to have you on board!</p>

                <div class="steps">
                    <div class="step">
                        <div class="step-icon">📍</div>
                        <div class="step-text">
                            <strong>Select your zone</strong>
                            <span>Choose your neighbourhood for pickup & delivery.</span>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-icon">🧺</div>
                        <div class="step-text">
                            <strong>Schedule a pickup</strong>
                            <span>Pick a date, time, and the services you need.</span>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-icon">💳</div>
                        <div class="step-text">
                            <strong>Top up your wallet</strong>
                            <span>Fund your wallet for seamless one-tap payments.</span>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-icon">✨</div>
                        <div class="step-text">
                            <strong>Receive fresh laundry</strong>
                            <span>We deliver right to your door.</span>
                        </div>
                    </div>
                </div>

                <p style="margin-top:20px">If you have any questions, reply to this email or reach us via the app.</p>
                <p>Happy washing! 🫧</p>
                <p style="color:#999;font-size:13px">— The DesiredWash Team</p>
            </div>
            <div class="footer">
                © {{ date('Y') }} DesiredWash · All rights reserved<br>
                You received this email because you signed up for DesiredWash.
            </div>
        </div>
    </div>
</body>
</html>
