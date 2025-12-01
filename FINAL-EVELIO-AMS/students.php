<?php
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Students</title>
    <style>
        body{font-family: Arial, sans-serif;}
        .container{max-width:1100px;margin:20px auto;}
        input,select{padding:8px;width:100%;}
        table{width:100%;border-collapse:collapse;margin-top:10px;}
        th,td{border:1px solid #ddd;padding:8px;}
        tr:hover{background:#F0F0F0;cursor:pointer;}
    </style>
    <script>
        let page=1,limit=10,q="",sort='grade_level',dir='ASC',pendingId=0,confirmCb=null;
        function loadStudents(){
            fetch('getStudents.php?q='+encodeURIComponent(q)+'&page='+page+'&limit='+limit+'&sort='+encodeURIComponent(sort)+'&dir='+encodeURIComponent(dir))
                .then(r=>r.text()).then(html=>{document.getElementById('list').innerHTML=html});
        }
        function searchInput(v){ q=v.trim(); page=1; loadStudents(); }
        function setSort(s){ dir = (sort===s && dir==='ASC')? 'DESC':'ASC'; sort=s; loadStudents(); }
        function viewStudent(id){ window.location.href='student_view.php?id='+id; }
        function updateStudent(id){ window.location.href='student_update.php?id='+id; }
        function openModal(title, body, cb){ document.getElementById('m_title').innerText=title; document.getElementById('m_body').innerText=body; confirmCb=cb; document.getElementById('modal').style.display='flex'; }
        function closeModal(){ document.getElementById('modal').style.display='none'; confirmCb=null; }
        function confirmModal(){ if(confirmCb) confirmCb(); }
        function deleteStudent(id){ openModal('Delete Student','Are you sure?', function(){ fetch('deleteStudent.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id}).then(r=>r.json()).then(j=>{ closeModal(); if(j.success){ loadStudents(); } else { alert('Failed'); } }); }); }
        function createAccount(id){ openModal('Create Account','Create school account for this student?', function(){ fetch('createStudentAccount.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id}).then(r=>r.json()).then(j=>{ closeModal(); alert(j.success? 'Account created':'Failed'); }); }); }
        function editAccount(id){ const u = prompt('Username:'); if(u===null) return; fetch('editStudentAccount.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id+'&username='+encodeURIComponent(u)}).then(r=>r.json()).then(j=>{ alert(j.success? 'Updated':'Failed'); }); }
        window.onload = loadStudents;
    </script>
</head>
<body>
<div class="container">
    <h3>Manage Students</h3>
    <p><a href="students_add.php">Add Student (manual)</a></p>
    <input type="text" placeholder="Search name or LRN" oninput="searchInput(this.value)">
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
