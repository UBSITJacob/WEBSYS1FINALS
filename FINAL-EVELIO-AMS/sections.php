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
    <title>Manage Sections</title>
    <style>
        body{font-family: Arial, sans-serif;}
        .container{max-width:1000px;margin:20px auto;}
        input,select{padding:8px;width:100%;}
        table{width:100%;border-collapse:collapse;margin-top:10px;}
        th,td{border:1px solid #ddd;padding:8px;}
        tr:hover{background:#F0F0F0;cursor:pointer;}
        .row{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-top:10px;}
    </style>
    <script>
        let page=1,limit=10,q="",sort='grade_level',dir='ASC',confirmCb=null;
        function loadSections(){
            fetch('getSections.php?q='+encodeURIComponent(q)+'&page='+page+'&limit='+limit+'&sort='+encodeURIComponent(sort)+'&dir='+encodeURIComponent(dir))
                .then(r=>r.text()).then(html=>{document.getElementById('list').innerHTML=html});
        }
        function searchInput(v){ q=v.trim(); page=1; loadSections(); }
        function setSort(s){ dir = (sort===s && dir==='ASC')? 'DESC':'ASC'; sort=s; loadSections(); }
        function addSection(){
            const name = document.getElementById('s_name').value.trim();
            const dept = document.getElementById('s_dept').value;
            const grade = document.getElementById('s_grade').value;
            const strand = document.getElementById('s_strand').value;
            const cap = document.getElementById('s_capacity').value;
            if(!name){ alert('Enter name'); return; }
            fetch('addSection.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'name='+encodeURIComponent(name)+'&department='+encodeURIComponent(dept)+'&grade_level='+encodeURIComponent(grade)+'&strand='+encodeURIComponent(strand)+'&capacity='+encodeURIComponent(cap)})
                .then(r=>r.json()).then(j=>{ if(j.success){
                    document.getElementById('s_name').value='';
                    loadSections();
                }else{ alert('Failed'); } });
        }
        function editSection(id){
            const newName = prompt('Section name:'); if(newName===null) return;
            const newCap = prompt('Capacity:'); if(newCap===null) return;
            fetch('updateSection.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id+'&name='+encodeURIComponent(newName)+'&capacity='+encodeURIComponent(newCap)})
                .then(r=>r.json()).then(j=>{ if(j.success){ loadSections(); } else { alert('Failed'); } });
        }
        function openModal(title, body, cb){ document.getElementById('m_title').innerText=title; document.getElementById('m_body').innerText=body; confirmCb=cb; document.getElementById('modal').style.display='flex'; }
        function closeModal(){ document.getElementById('modal').style.display='none'; confirmCb=null; }
        function confirmModal(){ if(confirmCb) confirmCb(); }
        function deleteSection(id){
            openModal('Delete Section','Are you sure?', function(){
                fetch('deleteSection.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id})
                    .then(r=>r.json()).then(j=>{ closeModal(); if(j.success){ loadSections(); } else { alert('Failed'); } });
            });
        }
        window.onload = loadSections;
    </script>
</head>
<body>
<div class="container">
    <h3>Manage Sections</h3>
    <input type="text" placeholder="Search sections..." oninput="searchInput(this.value)">
    <div class="row">
        <div><label>Name</label><input id="s_name" type="text"></div>
        <div><label>Department</label><select id="s_dept"><option>JHS</option><option>SHS</option></select></div>
        <div><label>Grade Level</label>
            <select id="s_grade">
                <option>Grade 7</option><option>Grade 8</option><option>Grade 9</option><option>Grade 10</option><option>Grade 11</option><option>Grade 12</option>
            </select>
        </div>
        <div><label>Strand</label><select id="s_strand"><option value="">None</option><option>HUMSS</option><option>TVL</option></select></div>
        <div><label>Capacity</label><input id="s_capacity" type="number" value="40"></div>
    </div>
    <div style="margin-top:10px;"><button onclick="addSection()">Add Section</button></div>
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
