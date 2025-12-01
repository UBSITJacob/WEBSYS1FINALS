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
    <title>Applicants</title>
    <style>
        body{font-family: Arial, sans-serif;}
        .container{max-width:1000px;margin:20px auto;}
        input{padding:8px;width:100%;}
        table{width:100%;border-collapse:collapse;margin-top:10px;}
        th,td{border:1px solid #ddd;padding:8px;}
        tr:hover{background:#F0F0F0;cursor:pointer;}
        .actions button{margin-right:6px;}
    </style>
    <script>
        let page=1,limit=10,q="",sort='created_at',dir='DESC',confirmCb=null;
        function loadApplicants(){
            fetch("getApplicants.php?q="+encodeURIComponent(q)+"&page="+page+"&limit="+limit+"&sort="+encodeURIComponent(sort)+"&dir="+encodeURIComponent(dir))
                .then(r=>r.text())
                .then(html=>{ document.getElementById('list').innerHTML = html; })
                .catch(()=>{ document.getElementById('list').innerHTML = 'Error'; });
        }
        function searchInput(v){ q=v.trim(); page=1; loadApplicants(); }
        function setSort(s){ dir = (sort===s && dir==='ASC')? 'DESC':'ASC'; sort=s; loadApplicants(); }
        function openModal(title, body, cb){ document.getElementById('m_title').innerText=title; document.getElementById('m_body').innerText=body; confirmCb=cb; document.getElementById('modal').style.display='flex'; }
        function closeModal(){ document.getElementById('modal').style.display='none'; confirmCb=null; }
        function confirmModal(){ if(confirmCb) confirmCb(); }
        function approve(id){
            openModal('Approve Applicant','Approve this applicant?', function(){
                fetch('applicant_approve.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+encodeURIComponent(id)})
                .then(r=>r.json()).then(j=>{ closeModal(); if(j.success){ loadApplicants(); } else { alert('Failed'); } });
            });
        }
        function decline(id){
            openModal('Decline Applicant','Decline this applicant?', function(){
                fetch('applicant_decline.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+encodeURIComponent(id)})
                .then(r=>r.json()).then(j=>{ closeModal(); if(j.success){ loadApplicants(); } else { alert('Failed'); } });
            });
        }
        function viewApplicant(id){ window.location.href = 'applicant_view.php?id='+id; }
        window.onload = loadApplicants;
    </script>
</head>
<body>
<div class="container">
    <h3>Applicants</h3>
    <input type="text" placeholder="Search name or LRN" oninput="searchInput(this.value)">
    <div id="list"></div>
    <div id="modal" class="modal" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,.4);display:none;align-items:center;justify-content:center;">
        <div class="box" style="background:#fff;padding:16px;border-radius:8px;max-width:420px;width:90%;">
            <h4 id="m_title"></h4>
            <p id="m_body"></p>
            <div style="text-align:right;margin-top:10px;"><button onclick="closeModal()">Cancel</button> <button onclick="confirmModal()">Confirm</button></div>
        </div>
    </div>
</div>
</body>
</html>
