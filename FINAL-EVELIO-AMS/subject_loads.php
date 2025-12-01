<?php
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
include "dbconfig.php";
$teachers = $pdo->query("SELECT id, full_name FROM teachers ORDER BY full_name")->fetchAll();
$subjects = $pdo->query("SELECT id, name FROM subjects ORDER BY name")->fetchAll();
$sections = $pdo->query("SELECT id, name FROM sections ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subject Loads</title>
    <style>body{font-family: Arial, sans-serif;} .container{max-width:1000px;margin:20px auto;} input,select{padding:8px;width:100%;} table{width:100%;border-collapse:collapse;margin-top:10px;} th,td{border:1px solid #ddd;padding:8px;} tr:hover{background:#F0F0F0;cursor:pointer;} .row{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-top:10px;}</style>
    <script>
        let page=1,limit=10,q="",sort='school_year',dir='DESC',confirmCb=null;
        function loadLoads(){ fetch('getSubjectLoads.php?q='+encodeURIComponent(q)+'&page='+page+'&limit='+limit+'&sort='+encodeURIComponent(sort)+'&dir='+encodeURIComponent(dir)).then(r=>r.text()).then(html=>{document.getElementById('list').innerHTML=html}); }
        function searchInput(v){ q=v.trim(); page=1; loadLoads(); }
        function setSort(s){ dir = (sort===s && dir==='ASC')? 'DESC':'ASC'; sort=s; loadLoads(); }
        function addLoad(){
            const teacher_id = document.getElementById('l_teacher').value;
            const subject_id = document.getElementById('l_subject').value;
            const section_id = document.getElementById('l_section').value;
            const sy = document.getElementById('l_sy').value.trim();
            const sem = document.getElementById('l_sem').value;
            if(!sy){ alert('Enter school year'); return; }
            fetch('addSubjectLoad.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'teacher_id='+teacher_id+'&subject_id='+subject_id+'&section_id='+section_id+'&school_year='+encodeURIComponent(sy)+'&semester='+encodeURIComponent(sem)})
            .then(r=>r.json()).then(j=>{ if(j.success){ loadLoads(); } else { alert('Failed'); } });
        }
        function openModal(title, body, cb){ document.getElementById('m_title').innerText=title; document.getElementById('m_body').innerText=body; confirmCb=cb; document.getElementById('modal').style.display='flex'; }
        function closeModal(){ document.getElementById('modal').style.display='none'; confirmCb=null; }
        function confirmModal(){ if(confirmCb) confirmCb(); }
        function deleteLoad(id){ openModal('Delete Load','Are you sure?', function(){ fetch('deleteSubjectLoad.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id}).then(r=>r.json()).then(j=>{ closeModal(); if(j.success){ loadLoads(); } else { alert('Failed'); } }); }); }
        window.onload = loadLoads;
    </script>
</head>
<body>
<div class="container">
    <h3>Subject Loads</h3>
    <input type="text" placeholder="Search teacher/subject/section" oninput="searchInput(this.value)">
    <div class="row">
        <div><label>Teacher</label><select id="l_teacher"><?php foreach($teachers as $t){ echo '<option value="'.$t['id'].'">'.htmlspecialchars($t['full_name'],ENT_QUOTES,'UTF-8').'</option>'; } ?></select></div>
        <div><label>Subject</label><select id="l_subject"><?php foreach($subjects as $s){ echo '<option value="'.$s['id'].'">'.htmlspecialchars($s['name'],ENT_QUOTES,'UTF-8').'</option>'; } ?></select></div>
        <div><label>Section</label><select id="l_section"><?php foreach($sections as $sec){ echo '<option value="'.$sec['id'].'">'.htmlspecialchars($sec['name'],ENT_QUOTES,'UTF-8').'</option>'; } ?></select></div>
        <div><label>School Year</label><input id="l_sy" type="text" placeholder="2025-2026"></div>
        <div><label>Semester (SHS)</label><select id="l_sem"><option value="">None</option><option>First</option><option>Second</option></select></div>
    </div>
    <div style="margin-top:10px;"><button onclick="addLoad()">Add Load</button></div>
    <div id="list"></div>
    <div id="modal" class="modal" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,.4);display:none;align-items:center;justify-content:center;">
        <div class="box" style="background:#fff;padding:16px;border-radius:8px;max-width:420px;width:90%;">
            <h4 id="m_title"></h4>
            <p id="m_body"></p>
            <div style="text-align:right;margin-top:10px;"><button onclick="closeModal()">Cancel</button> <button onclick="confirmModal()">Confirm</button></div>
        </div>
    </div>
    <p><a href="admin_dashboard.php">Back</a></p>
</div>
</body>
</html>
