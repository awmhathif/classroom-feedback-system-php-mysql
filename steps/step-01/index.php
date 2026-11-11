<?php
require "includes/config.php";

$pageTitle = "Welcome";
require "includes/header.php";
?>

<section class="panel hero">
    <p class="eyebrow">Honest feedback for teaching</p>
    <h1>Find out what to change in your class, and show students they were heard.</h1>
    <p class="lead muted">Teachers run a short feedback round. Every student answers once, and names are never saved with answers. When the round closes, the teacher sees the clearest priorities first and posts a reply explaining what will change.</p>
</section>

<section class="section">
    <h2>How it works</h2>
    <div class="three">
        <div class="panel">
            <h3 class="step">1. Run a round</h3>
            <p class="muted">Start with seven ready questions or write your own. Share your class code with students.</p>
        </div>
        <div class="panel">
            <h3 class="step">2. Students answer once</h3>
            <p class="muted">Each student in the class gets one vote. While the round is open, nobody can see any scores or comments.</p>
        </div>
        <div class="panel">
            <h3 class="step">3. Decide and reply</h3>
            <p class="muted">See the lowest rated items first and compare with your last round. Then tell students what their feedback changed.</p>
        </div>
    </div>
</section>

<section class="panel accent">
    <h2>Our anonymity promise</h2>
    <p>Students give better answers when they know it is safe to be honest, so anonymity is built into how answers are stored:</p>
    <ul>
        <li>Answers are saved with no student id, no name and no time, in random order.</li>
        <li>Teachers never see who has or has not answered, during or after the round.</li>
        <li>If fewer than <?php echo MIN_RESPONSES; ?> students answer a round, no results or comments are shown at all.</li>
    </ul>
</section>

<?php require "includes/footer.php"; ?>
