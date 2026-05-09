<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>?v=2" type="image/x-icon">
    <title>Dreamlinks | AI Dream Analysis & Social Dream Journaling</title>
    <meta name="description" content="Log your dreams, discover hidden patterns with AI analysis, track celestial influences, and connect with a global community of dreamers on Dreamlinks.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .hero-section { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); padding: 100px 0; border-bottom: 1px solid #334155; }
        h1, h2 { font-family: 'Playfair Display', serif; }
        .feature-icon { font-size: 2rem; color: #818cf8; margin-bottom: 1rem; }
        .card { background: #1e293b; border: 1px solid #334155; color: #f8fafc; transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); border-color: #818cf8; }
        .btn-primary { background-color: #6366f1; border: none; padding: 12px 30px; font-weight: bold; }
        .btn-primary:hover { background-color: #4f46e5; }
        .text-gradient { background: linear-gradient(to right, #818cf8, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-EDLB3K5XWK"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-EDLB3K5XWK');
    </script>
	
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="#">Dream<span class="text-gradient">links</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/learn">Learn</a></li> 
                    <li class="nav-item"><a class="btn btn-outline-light ms-lg-3" href=" user/login">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-3 fw-bold mb-4">Decode the Language of Your <span class="text-gradient">Subconscious</span></h1>
                    <p class="lead mb-5 text-secondary">The world's first AI-powered dream journal. Log your nights, analyze recurring patterns, and sync your subconscious with celestial events.</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <a href="user/register" class="btn btn-primary btn-lg px-5">Start Your Free Journal</a>
                        <a href="learn" class="btn btn-outline-secondary btn-lg px-5">Explore AI Analysis</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="features" class="py-5 bg-dark">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-5">Why Use Dreamlinks?</h2>
                <p class="text-secondary">Advanced tools to help you understand what your mind is saying.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 p-4">
                        <div class="feature-icon">🤖</div>
                        <h3>AI Pattern Analysis</h3>
                        <p class="text-secondary">Our neural engine scans your logs to identify recurring themes, emotions, and symbols you might miss.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 p-4">
                        <div class="feature-icon">✨</div>
                        <h3>Celestial Syncing</h3>
                        <p class="text-secondary">Compare your dream data with lunar cycles, planetary alignments, and solar activity to find cosmic correlations.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 p-4">
                        <div class="feature-icon">📖</div>
                        <h3>Smart Journaling</h3>
                        <p class="text-secondary">Easily log dreams from any device. Secure, encrypted, and searchable.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="community" class="py-5 border-top border-secondary">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-5 mb-4">A Social Network for <span class="text-gradient">The Dreamers</span></h2>
                    <p class="lead">You aren't alone in the dream world. Connect with others who experience similar patterns.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2">✅ Share your dream logs (anonymously or public)</li>
                        <li class="mb-2">✅ Follow friends and explore their subconscious adventures</li>
                        <li class="mb-2">✅ Participate in global dream research and statistics</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="p-4 bg-secondary bg-opacity-10 rounded-4 border border-secondary">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary me-3" style="width: 50px; height: 50px;"></div>
                            <div>
                                <h6 class="mb-0">User #8421 Shared:</h6>
                                <small class="text-secondary">3 hours ago • #LucidDream #Flying</small>
                            </div>
                        </div>
                        <p class="fst-italic">"I was soaring over a neon city tonight. The AI noted this was my 3rd flight dream during a Full Moon..."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 bg-black text-center text-secondary border-top border-secondary">
        <div class="container">
            <p>&copy; 2026 Dreamlinks. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
