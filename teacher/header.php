<div class="header" id="header">
    <div class="title">Evelio AMS - Teacher Portal</div>
    <div class="controls">
        <button id="logoutBtn" style="background:#dc3545;">Sign Out</button>
    </div>
</div>

<script>
    // Simple logout logic
    document.getElementById('logoutBtn').addEventListener('click', () => {
        // This should hit a logout script that destroys the session
        alert('Logging out...');
        window.location.href = '../index.php'; 
    });
</script>