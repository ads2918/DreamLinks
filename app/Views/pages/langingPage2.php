<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamLinks | AI Subconscious Intelligence</title>
    <style>
        :root {
            --bg-color: #05070a;
            --card-bg: rgba(255, 255, 255, 0.03);
            --accent-glow: rgba(79, 70, 229, 0.4);
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
            --indigo: #6366f1;
            --gold: #fbbf24;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Subtle animated background */
        .stars {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 50% 50%, #111827 0%, #05070a 100%);
            z-index: -1;
        }

        .container {
            max-width: 900px;
            padding: 3rem;
            z-index: 1;
            text-align: center;
        }

        .status-badge {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            color: var(--indigo);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 2rem;
            display: inline-block;
        }

        h1 {
            font-size: 4rem;
            font-weight: 800;
            margin: 0 0 1.5rem 0;
            letter-spacing: -0.04em;
            background: linear-gradient(to bottom, #fff 40%, #64748b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-p {
            font-size: 1.35rem;
            color: var(--text-dim);
            line-height: 1.6;
            margin-bottom: 3.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 4rem;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 2rem;
            border-radius: 20px;
            transition: all 0.3s ease;
            text-align: left;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 0 30px var(--accent-glow);
            transform: translateY(-5px);
        }

        .card h3 {
            margin: 0 0 0.75rem 0;
            font-size: 1.1rem;
            color: #fff;
        }

        .card p {
            margin: 0;
            font-size: 0.95rem;
            color: var(--text-dim);
            line-height: 1.5;
        }

        .btn {
            background: var(--text-main);
            color: var(--bg-color);
            padding: 18px 36px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: opacity 0.2s;
            display: inline-block;
        }

        .btn:hover {
            opacity: 0.9;
        }

        footer {
            margin-top: 5rem;
            color: #475569;
            font-size: 0.85rem;
            letter-spacing: 0.02em;
        }
    </style>
</head>
<body>
    <div class="stars"></div>
    
    <div class="container">
        <div class="status-badge">Private Alpha // Scheduled for April 2026</div>
        
        <h1>DreamLinks</h1>
        <p class="hero-p">The first platform to correlate individual dreams with celestial cycles, global events, and the "Collective Pulse."</p>

        <div class="grid">
            <div class="card">
                <h3>Inner Vault</h3>
                <p>Privacy-first architecture ensuring total user sovereignty over personal journals.</p>
            </div>
            <div class="card">
                <h3>Collective Pulse</h3>
                <p>Anonymized global sentiment analysis mapping the shared human story.</p>
            </div>
            <div class="card">
                <h3>Contextual Engine</h3>
                <p>Overlay dream data against real-world triggers like lunar phases and global news.</p>
            </div>
        </div>

        <a href="mailto:media@dreamlinks.com" class="btn">Enter the Vault</a>

        <footer>
            &copy; 2026 DreamLinks
        </footer>
    </div>
</body>
</html>