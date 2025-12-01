<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ header('Location: index.php'); exit; }
$crud = new pdoCRUD();
$acc = $crud->getAccountById($_SESSION['account_id']);
$teacher = $crud->getAccountPerson('teacher',$acc['person_id']);
$q = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'family_name';
$dir = $_GET['dir'] ?? 'ASC';
$page = max(1,(int)($_GET['page'] ?? 1));
$limit = max(1,min(50,(int)($_GET['limit'] ?? 10)));
$rows = $crud->getAdvisoryStudentsForTeacher($acc['person_id'],$q,$page,$limit,$sort,$dir);
$total = $crud->countAdvisoryStudentsForTeacher($acc['person_id'],$q);
$section_id = (int)($teacher['advisory_section_id'] ?? 0);
$loads = $section_id? $crud->getTeacherLoadsForSection($acc['person_id'],$section_id) : [];
$selected = (int)($_GET['load_id'] ?? 0);
$date = $_GET['date'] ?? date('Y-m-d');
$enrolled = $selected? $crud->getEnrollmentsByLoad($selected) : [];
$enMap = [];
foreach($enrolled as $e){ $enMap[$e['id']] = $e['enrollment_id']; }
$gradeMap = [];
$attendanceMap = [];
if($selected){
    $grades = $crud->getGradesForLoad($selected);
    foreach($grades as $g){ $gradeMap[(int)$g['enrollment_id']] = $g['grade']; }
    $att = $crud->getAttendanceForLoadAndDate($selected,$date);
    foreach($att as $a){ $attendanceMap[(int)$a['student_id']] = $a['status']; }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Advisory Class</title>
    <style>
        body{font-family: Arial, sans-serif;}
        .container{max-width:1000px;margin:20px auto;}
        table{width:100%;border-collapse:collapse;margin-top:10px;}
        th,td{border:1px solid #ddd;padding:8px;}
        tr:hover{background:#F0F0F0;cursor:pointer;}
        .pager{margin-top:10px;display:flex;gap:6px;align-items:center;}
        .modal{position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,.4);display:none;align-items:center;justify-content:center;}
        .modal .box{background:#fff;padding:16px;border-radius:8px;max-width:420px;width:90%;}
    </style>
    <script>
        let page=<?php echo (int)$page; ?>, limit=<?php echo (int)$limit; ?>, q="<?php echo htmlspecialchars($q,ENT_QUOTES,'UTF-8'); ?>", sort="<?php echo htmlspecialchars($sort,ENT_QUOTES,'UTF-8'); ?>", dir="<?php echo htmlspecialchars($dir,ENT_QUOTES,'UTF-8'); ?>";
        function selectLoad(){ const id = document.getElementById('load').value; const d = document.getElementById('date').value; const url = new URL(window.location.href); url.searchParams.set('load_id', id); url.searchParams.set('date', d); window.location.href = url.toString(); }
        function setSort(s){ dir = (sort===s && dir==='ASC')? 'DESC':'ASC'; sort=s; reload(); }
        function searchInput(v){ q=v.trim(); page=1; reload(); }
        function prev(){ if(page>1){ page--; reload(); } }
        function next(){ page++; reload(); }
        function reload(){ window.location.href = 'advisory_class.php?q='+encodeURIComponent(q)+'&sort='+encodeURIComponent(sort)+'&dir='+encodeURIComponent(dir)+'&page='+page+'&limit='+limit; }
        function openModal(title, body){ document.getElementById('m_title').innerText=title; document.getElementById('m_body').innerText=body; document.getElementById('modal').style.display='flex'; }
        function closeModal(){ document.getElementById('modal').style.display='none'; }
        function saveGrade(eid){ const g = prompt('Enter grade:'); if(g===null) return; fetch('saveGrade.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'enrollment_id='+eid+'&grade='+encodeURIComponent(g)}).then(r=>r.json()).then(j=>{ alert(j.success? 'Saved':'Failed'); }); }
        function saveAttendance(sid){ const d = document.getElementById('date').value; const lid = document.getElementById('load').value; const st = document.getElementById('att_'+sid).value; fetch('saveAttendance.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'student_id='+sid+'&subject_load_id='+lid+'&date='+encodeURIComponent(d)+'&status='+encodeURIComponent(st)}).then(r=>r.json()).then(j=>{ alert(j.success? 'Saved':'Failed'); }); }
    </script>
</head>
<body>
<div class="container">
    <h3>Advisory Class</h3>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
        <div>
            <label>Subject Load</label>
            <select id="load" onchange="selectLoad()">
                <option value="0">Select load</option>
                <?php foreach($loads as $l){ $label = htmlspecialchars(($l['subject_code']??'').' - '.($l['subject_name']??''),ENT_QUOTES,'UTF-8'); echo '<option value="'.$l['id'].'"'.($selected==$l['id']?' selected':'').'>'.$label.'</option>'; } ?>
            </select>
        </div>
        <div>
            <label>Date</label>
            <input id="date" type="date" value="<?php echo htmlspecialchars($date,ENT_QUOTES,'UTF-8'); ?>" onchange="selectLoad()">
        </div>
        <div>
            <label>Search</label>
            <input type="text" placeholder="Search name or LRN" oninput="searchInput(this.value)">
        </div>
    </div>
    <div style="display:flex;gap:8px;margin-top:8px;align-items:center;">
        <button onclick="exportAttendance()">Export Attendance CSV</button>
        <button onclick="exportGrades()">Export Grades CSV</button>
        <label style="margin-left:auto;">Quick Filter</label>
        <select id="quick_filter" onchange="applyFilter()"><option value="all">All</option><option value="present">Present</option><option value="absent">Absent</option><option value="tardy">Tardy</option><option value="unmarked">Unmarked</option></select>
    </div>
    <?php if($selected){
        $p=0;$a=0;$t=0; $total = count($enrolled);
        foreach($enrolled as $e){ $sid = (int)$e['id']; $st = $attendanceMap[$sid] ?? null; if($st==='present') $p++; elseif($st==='absent') $a++; elseif($st==='tardy') $t++; }
        $pct = $total>0 ? round(($p/$total)*100) : 0;
        $un = $total - ($p+$a+$t);
        echo '<div style="margin-top:8px;padding:8px;border:1px solid #ddd;background:#F9F9F9;">Present: '.(int)$p.'/'.(int)$total.' ('.$pct.'%) | Absent: '.(int)$a.'/'.(int)$total.' | Tardy: '.(int)$t.'/'.(int)$total.' | Unmarked: '.(int)$un.'</div>';
    } ?>
    <table>
        <tr>
            <th onclick="setSort('lrn')">LRN</th>
            <th onclick="setSort('family_name')">Name</th>
            <th onclick="setSort('grade_level')">Grade</th>
            <th>Grade</th>
            <th>Attendance</th>
            <th>Actions</th>
        </tr>
        <?php if(!$rows){ ?>
            <tr><td colspan="4" align="center">No Records Found...</td></tr>
        <?php } else { foreach($rows as $r){ $eid = $selected && isset($enMap[$r['id']]) ? (int)$enMap[$r['id']] : 0; $rowSt = $attendanceMap[(int)$r['id']] ?? 'unmarked'; ?>
            <tr id="row_<?php echo (int)$r['id']; ?>" data-row="1" data-status="<?php echo htmlspecialchars($rowSt,ENT_QUOTES,'UTF-8'); ?>">
                <td><?php echo htmlspecialchars($r['lrn'],ENT_QUOTES,'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($r['family_name'].', '.$r['first_name'],ENT_QUOTES,'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($r['grade_level'],ENT_QUOTES,'UTF-8'); ?></td>
                <td><?php if($eid){ $cur = isset($gradeMap[$eid])? htmlspecialchars($gradeMap[$eid],ENT_QUOTES,'UTF-8') : '-'; echo $cur.' '; ?><button onclick="saveGrade(<?php echo $eid; ?>)">Save</button><?php } else { echo '-'; } ?></td>
                <td><?php if($selected){ $curSt = $attendanceMap[(int)$r['id']] ?? ''; ?>
                    <select id="att_<?php echo (int)$r['id']; ?>">
                        <option <?php echo $curSt==='present'?'selected':''; ?>>present</option>
                        <option <?php echo $curSt==='absent'?'selected':''; ?>>absent</option>
                        <option <?php echo $curSt==='tardy'?'selected':''; ?>>tardy</option>
                    </select>
                    <button onclick="saveAttendanceNew(<?php echo (int)$r['id']; ?>)">Save</button>
                <?php } else { echo '-'; } ?></td>
                <td>
                    <button onclick="openModal('Student', '<?php echo htmlspecialchars($r['family_name'].', '.$r['first_name'].' | LRN: '.$r['lrn'],ENT_QUOTES,'UTF-8'); ?>')">View</button>
                    <a href="grades.php" style="margin-left:6px;">Grades</a>
                    <a href="attendance.php" style="margin-left:6px;">Attendance</a>
                </td>
            </tr>
        <?php }} ?>
    </table>
    <div class="pager">
        <button onclick="prev()">Prev</button>
        <span>Page <?php echo (int)$page; ?> of <?php echo max(1, ceil($total/$limit)); ?></span>
        <button onclick="next()">Next</button>
    </div>
    <p><a href="teacher_dashboard.php">Back</a></p>
    <script>
        function exportAttendance(){ const lid = document.getElementById('load').value; const d = document.getElementById('date').value; if(lid==='0'||!d){ alert('Select load and date'); return; } window.location.href='advisory_attendance_export.php?load_id='+lid+'&date='+encodeURIComponent(d); }
        function exportGrades(){ const lid = document.getElementById('load').value; if(lid==='0'){ alert('Select load'); return; } window.location.href='advisory_grades_export.php?load_id='+lid; }
        function applyFilter(){ const f = document.getElementById('quick_filter').value; const rows = document.querySelectorAll('table tr[data-row="1"]'); rows.forEach(r=>{ r.style.background=''; const st = r.dataset.status||'unmarked'; if(f==='all') return; if(st===f){ r.style.background = f==='present'? '#DFF0D8' : (f==='absent'? '#F2DEDE' : (f==='tardy'? '#FCF8E3' : '')); } }); }
        function saveAttendanceNew(sid){ const d = document.getElementById('date').value; const lid = document.getElementById('load').value; const st = document.getElementById('att_'+sid).value; fetch('saveAttendance.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'student_id='+sid+'&subject_load_id='+lid+'&date='+encodeURIComponent(d)+'&status='+encodeURIComponent(st)}).then(r=>r.json()).then(j=>{ if(j.success){ const tr = document.getElementById('row_'+sid); if(tr){ tr.dataset.status = st; } applyFilter(); alert('Saved'); } else { alert('Failed'); } }); }
    </script>
</div>
<div id="modal" class="modal"><div class="box"><h4 id="m_title"></h4><p id="m_body"></p><div style="text-align:right;margin-top:10px;"><button onclick="closeModal()">Close</button></div></div></div>
</body>
</html>
