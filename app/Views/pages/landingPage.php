<section class="dl-land-hero">
	<div class="dl-land-copy">
		<div class="dl-eyebrow">Dream journal · AI readings · moon tracking</div>
		<h1>Decode the language of your subconscious.</h1>
		<svg class="dl-squiggle" viewBox="0 0 260 18" preserveAspectRatio="none" aria-hidden="true"><path d="M3 10 C 20 0, 36 18, 54 9 S 88 0, 106 9 S 140 18, 158 9 S 192 0, 210 9 S 244 18, 257 8" fill="none" stroke="#18b7a6" stroke-width="4" stroke-linecap="round"/></svg>
		<p>Log your dreams before they fade, get an AI reading of the symbols and feelings that keep coming back, and see what the people you trust have been dreaming.</p>
		<div class="dl-land-ctas">
			<a class="dl-btn" href="<?= site_url('user/register'); ?>">Start your journal</a>
			<a class="dl-btn is-plain" href="<?= site_url('user/login'); ?>">Log in</a>
		</div>
		<p class="dl-hint">Free. New accounts are approved by hand.</p>
	</div>
	<div class="dl-portal is-big" aria-hidden="true">
		<canvas></canvas>
		<div class="dl-portal-moon"></div>
		<div class="dl-portal-label"></div>
	</div>
</section>

<section class="dl-land-features" aria-label="What DreamLinks does">
	<article class="dl-sticker dl-land-feature">
		<span class="dl-land-tag" style="background:var(--pink)">01</span>
		<h2>Log it before it fades</h2>
		<p>Write it down the moment you wake, from your phone or computer. Tag it, and keep each dream private or share it.</p>
	</article>
	<article class="dl-sticker dl-land-feature">
		<span class="dl-land-tag" style="background:var(--teal)">02</span>
		<h2>See the patterns</h2>
		<p>An AI reading of your journal picks out the recurring symbols, moods and themes you might not notice on your own.</p>
	</article>
	<article class="dl-sticker dl-land-feature">
		<span class="dl-land-tag" style="background:var(--yellow);color:var(--ink)">03</span>
		<h2>Read the sky too</h2>
		<p>DreamLinks tracks the lunar cycle, and your reading points out the moon and sky events that line up with your dreams.</p>
	</article>
</section>

<section class="dl-land-community">
	<div>
		<div class="dl-eyebrow">The commons</div>
		<h2>Dream with the people you trust.</h2>
		<p>Link with friends and read each other's shared dreams in one feed. You choose who you link with, and you can block anyone.</p>
		<ul class="dl-land-list">
			<li>Share a dream with your links, or keep it to yourself</li>
			<li>Search for other dreamers and send link requests</li>
			<li>Accept or decline every request that comes in</li>
		</ul>
	</div>
	<figure class="dl-sticker dl-land-dream">
		<figcaption class="dl-eyebrow">Example entry</figcaption>
		<div class="dl-land-dream-head">
			<span class="dl-avatar" aria-hidden="true"></span>
			<div>
				<strong>nightswimmer</strong>
				<div class="dl-hint">Last night</div>
			</div>
		</div>
		<blockquote>"I was flying over a neon city, and every window was a different song. My sister was on the roof waving me down."</blockquote>
		<div class="dl-land-chips">
			<span>#flying</span><span>#city</span><span>#sister</span>
		</div>
	</figure>
</section>

<section class="dl-sticker dl-land-cta">
	<h2>Tonight's dream is already on its way.</h2>
	<a class="dl-btn" href="<?= site_url('user/register'); ?>">Start your journal</a>
</section>
