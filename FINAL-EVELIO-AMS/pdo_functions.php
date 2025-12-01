<?php
class pdoCRUD{
    private $pdo;

    function __construct(){
        $host = "localhost";
        $user = "root";
        $pass = "";
        $dbname = "evelio_ams_db";
        $charset = "utf8mb4";
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $this->pdo = new PDO($dsn,$user,$pass,[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }

    public function login($email,$password){
        $q = $this->pdo->prepare("SELECT * FROM accounts WHERE email = :email LIMIT 1");
        $q->execute([':email'=>$email]);
        $u = $q->fetch();
        if(!$u) return false;
        if(!password_verify($password,$u['password_hash'])) return false;
        return $u;
    }

    public function getAccountById($id){
        $q = $this->pdo->prepare("SELECT * FROM accounts WHERE id = :id");
        $q->execute([':id'=>$id]);
        return $q->fetch();
    }

    public function changePassword($accountId,$current,$new){
        $acc = $this->getAccountById($accountId);
        if(!$acc) return false;
        if(!password_verify($current,$acc['password_hash'])) return false;
        if(strlen($new) < 8) return false;
        $h = password_hash($new, PASSWORD_DEFAULT);
        $u = $this->pdo->prepare("UPDATE accounts SET password_hash = :h, first_login_required = 0 WHERE id = :id");
        $u->execute([':h'=>$h, ':id'=>$accountId]);
        return true;
    }

    public function getAccountPerson($person_type,$person_id){
        if($person_type==='admin'){
            $s = $this->pdo->prepare("SELECT * FROM admins WHERE id = :id");
        }elseif($person_type==='teacher'){
            $s = $this->pdo->prepare("SELECT * FROM teachers WHERE id = :id");
        }else{
            $s = $this->pdo->prepare("SELECT * FROM students WHERE id = :id");
        }
        $s->execute([':id'=>$person_id]);
        return $s->fetch();
    }

    public function insertApplicant($data){
        $sql = "INSERT INTO applicants(
            department,grade_level,strand,student_type,
            family_name,first_name,middle_name,suffix,birthdate,birthplace,religion,civil_status,sex,
            mobile,email,
            curr_house_street,curr_barangay,curr_city,curr_province,curr_zip,
            perm_house_street,perm_barangay,perm_city,perm_province,perm_zip,
            elem_name,elem_address,elem_year_graduated,
            last_school_name,last_school_address,
            jhs_name,jhs_address,jhs_year_graduated,
            lrn,
            guardian_last_name,guardian_first_name,guardian_middle_name,guardian_contact,guardian_occupation,guardian_address,guardian_relationship,
            mother_last_name,mother_first_name,mother_middle_name,mother_contact,mother_occupation,mother_address,
            father_last_name,father_first_name,father_middle_name,father_contact,father_occupation,father_address
        ) VALUES (
            :department,:grade_level,:strand,:student_type,
            :family_name,:first_name,:middle_name,:suffix,:birthdate,:birthplace,:religion,:civil_status,:sex,
            :mobile,:email,
            :curr_house_street,:curr_barangay,:curr_city,:curr_province,:curr_zip,
            :perm_house_street,:perm_barangay,:perm_city,:perm_province,:perm_zip,
            :elem_name,:elem_address,:elem_year_graduated,
            :last_school_name,:last_school_address,
            :jhs_name,:jhs_address,:jhs_year_graduated,
            :lrn,
            :guardian_last_name,:guardian_first_name,:guardian_middle_name,:guardian_contact,:guardian_occupation,:guardian_address,:guardian_relationship,
            :mother_last_name,:mother_first_name,:mother_middle_name,:mother_contact,:mother_occupation,:mother_address,
            :father_last_name,:father_first_name,:father_middle_name,:father_contact,:father_occupation,:father_address
        )";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return $this->pdo->lastInsertId();
    }

    public function getApplicants($q,$page,$limit,$sort='created_at',$dir='DESC'){
        $offset = ($page-1)*$limit;
        $q = "%".$q."%";
        $allowed = ['created_at','family_name','lrn','grade_level'];
        if(!in_array($sort,$allowed)) $sort = 'created_at';
        $dir = strtoupper($dir)==='ASC' ? 'ASC' : 'DESC';
        $stmt = $this->pdo->prepare("SELECT * FROM applicants WHERE (CONCAT(family_name,' ',first_name) LIKE :q OR lrn LIKE :q) AND status='pending' ORDER BY $sort $dir LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':q',$q, PDO::PARAM_STR);
        $stmt->bindValue(':limit',(int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset',(int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countApplicants($q){
        $q = "%".$q."%";
        $c = $this->pdo->prepare("SELECT COUNT(*) c FROM applicants WHERE (CONCAT(family_name,' ',first_name) LIKE :q OR lrn LIKE :q) AND status='pending'");
        $c->execute([':q'=>$q]);
        return (int)$c->fetch()['c'];
    }

    public function getApplicantById($id){
        $s = $this->pdo->prepare("SELECT * FROM applicants WHERE id = :id");
        $s->execute([':id'=>$id]);
        return $s->fetch();
    }

    public function approveApplicant($id){
        $this->pdo->beginTransaction();
        $a = $this->getApplicantById($id);
        if(!$a){ $this->pdo->rollBack(); return false; }
        $insS = $this->pdo->prepare("INSERT INTO students(
            lrn,department,grade_level,strand,student_type,advisory_section_id,
            family_name,first_name,middle_name,suffix,birthdate,birthplace,religion,civil_status,sex,
            mobile,email,
            curr_house_street,curr_barangay,curr_city,curr_province,curr_zip,
            perm_house_street,perm_barangay,perm_city,perm_province,perm_zip,
            elem_name,elem_address,elem_year_graduated,
            last_school_name,last_school_address,
            jhs_name,jhs_address,jhs_year_graduated,
            guardian_last_name,guardian_first_name,guardian_middle_name,guardian_contact,guardian_occupation,guardian_address,guardian_relationship,
            mother_last_name,mother_first_name,mother_middle_name,mother_contact,mother_occupation,mother_address,
            father_last_name,father_first_name,father_middle_name,father_contact,father_occupation,father_address
        ) VALUES (
            :lrn,:department,:grade_level,:strand,:student_type,NULL,
            :family_name,:first_name,:middle_name,:suffix,:birthdate,:birthplace,:religion,:civil_status,:sex,
            :mobile,:email,
            :curr_house_street,:curr_barangay,:curr_city,:curr_province,:curr_zip,
            :perm_house_street,:perm_barangay,:perm_city,:perm_province,:perm_zip,
            :elem_name,:elem_address,:elem_year_graduated,
            :last_school_name,:last_school_address,
            :jhs_name,:jhs_address,:jhs_year_graduated,
            :guardian_last_name,:guardian_first_name,:guardian_middle_name,:guardian_contact,:guardian_occupation,:guardian_address,:guardian_relationship,
            :mother_last_name,:mother_first_name,:mother_middle_name,:mother_contact,:mother_occupation,:mother_address,
            :father_last_name,:father_first_name,:father_middle_name,:father_contact,:father_occupation,:father_address
        )");
        $insS->execute([
            ':lrn'=>$a['lrn'], ':department'=>$a['department'], ':grade_level'=>$a['grade_level'], ':strand'=>$a['strand'], ':student_type'=>$a['student_type'],
            ':family_name'=>$a['family_name'], ':first_name'=>$a['first_name'], ':middle_name'=>$a['middle_name'], ':suffix'=>$a['suffix'], ':birthdate'=>$a['birthdate'], ':birthplace'=>$a['birthplace'], ':religion'=>$a['religion'], ':civil_status'=>$a['civil_status'], ':sex'=>$a['sex'],
            ':mobile'=>$a['mobile'], ':email'=>"S-".$a['lrn']."@evelio.ams.edu",
            ':curr_house_street'=>$a['curr_house_street'], ':curr_barangay'=>$a['curr_barangay'], ':curr_city'=>$a['curr_city'], ':curr_province'=>$a['curr_province'], ':curr_zip'=>$a['curr_zip'],
            ':perm_house_street'=>$a['perm_house_street'], ':perm_barangay'=>$a['perm_barangay'], ':perm_city'=>$a['perm_city'], ':perm_province'=>$a['perm_province'], ':perm_zip'=>$a['perm_zip'],
            ':elem_name'=>$a['elem_name'], ':elem_address'=>$a['elem_address'], ':elem_year_graduated'=>$a['elem_year_graduated'],
            ':last_school_name'=>$a['last_school_name'], ':last_school_address'=>$a['last_school_address'],
            ':jhs_name'=>$a['jhs_name'], ':jhs_address'=>$a['jhs_address'], ':jhs_year_graduated'=>$a['jhs_year_graduated'],
            ':guardian_last_name'=>$a['guardian_last_name'], ':guardian_first_name'=>$a['guardian_first_name'], ':guardian_middle_name'=>$a['guardian_middle_name'], ':guardian_contact'=>$a['guardian_contact'], ':guardian_occupation'=>$a['guardian_occupation'], ':guardian_address'=>$a['guardian_address'], ':guardian_relationship'=>$a['guardian_relationship'],
            ':mother_last_name'=>$a['mother_last_name'], ':mother_first_name'=>$a['mother_first_name'], ':mother_middle_name'=>$a['mother_middle_name'], ':mother_contact'=>$a['mother_contact'], ':mother_occupation'=>$a['mother_occupation'], ':mother_address'=>$a['mother_address'],
            ':father_last_name'=>$a['father_last_name'], ':father_first_name'=>$a['father_first_name'], ':father_middle_name'=>$a['father_middle_name'], ':father_contact'=>$a['father_contact'], ':father_occupation'=>$a['father_occupation'], ':father_address'=>$a['father_address']
        ]);
        $studentId = $this->pdo->lastInsertId();
        $insA = $this->pdo->prepare("INSERT INTO accounts(email,username,password_hash,role,first_login_required,person_type,person_id) VALUES(:email,:username,:password_hash,'student',1,'student',:pid)");
        $insA->execute([
            ':email'=>"S-".$a['lrn']."@evelio.ams.edu",
            ':username'=>strtolower($a['family_name']).$studentId,
            ':password_hash'=>password_hash('1', PASSWORD_DEFAULT),
            ':pid'=>$studentId
        ]);
        $del = $this->pdo->prepare("DELETE FROM applicants WHERE id = :id");
        $del->execute([':id'=>$id]);
        $this->pdo->commit();
        return true;
    }

    public function declineApplicant($id){
        $d = $this->pdo->prepare("UPDATE applicants SET status = 'declined' WHERE id = :id");
        $d->execute([':id'=>$id]);
        return true;
    }

    // Sections
    public function getSections($q,$page,$limit,$sort='grade_level',$dir='ASC'){
        $offset = ($page-1)*$limit;
        $qLike = "%".$q."%";
        $allowed = ['name','department','grade_level','strand','capacity'];
        if(!in_array($sort,$allowed)) $sort = 'grade_level';
        $dir = strtoupper($dir)==='DESC' ? 'DESC' : 'ASC';
        $s = $this->pdo->prepare("SELECT * FROM sections WHERE name LIKE :q ORDER BY $sort $dir, name ASC LIMIT :limit OFFSET :offset");
        $s->bindValue(':q',$qLike,PDO::PARAM_STR);
        $s->bindValue(':limit',(int)$limit,PDO::PARAM_INT);
        $s->bindValue(':offset',(int)$offset,PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }

    public function countSections($q){
        $qLike = "%".$q."%";
        $s = $this->pdo->prepare("SELECT COUNT(*) c FROM sections WHERE name LIKE :q");
        $s->execute([':q'=>$qLike]);
        return (int)$s->fetch()['c'];
    }

    public function addSection($name,$department,$grade_level,$strand,$capacity){
        $i = $this->pdo->prepare("INSERT INTO sections(name,department,grade_level,strand,capacity) VALUES(:name,:department,:grade_level,:strand,:capacity)");
        $i->execute([':name'=>$name, ':department'=>$department, ':grade_level'=>$grade_level, ':strand'=>$strand?:null, ':capacity'=>(int)$capacity]);
        return $this->pdo->lastInsertId();
    }

    public function updateSection($id,$name,$department,$grade_level,$strand,$capacity){
        $u = $this->pdo->prepare("UPDATE sections SET name=:name, department=:department, grade_level=:grade_level, strand=:strand, capacity=:capacity WHERE id=:id");
        return $u->execute([':id'=>$id, ':name'=>$name, ':department'=>$department, ':grade_level'=>$grade_level, ':strand'=>$strand?:null, ':capacity'=>(int)$capacity]);
    }

    public function deleteSection($id){
        $d = $this->pdo->prepare("DELETE FROM sections WHERE id=:id");
        return $d->execute([':id'=>$id]);
    }

    // Teachers
    public function getTeachers($q,$page,$limit,$sort='full_name',$dir='ASC'){
        $offset = ($page-1)*$limit;
        $qLike = "%".$q."%";
        $allowed = ['full_name','faculty_id','email','active'];
        if(!in_array($sort,$allowed)) $sort = 'full_name';
        $dir = strtoupper($dir)==='DESC' ? 'DESC' : 'ASC';
        $s = $this->pdo->prepare("SELECT * FROM teachers WHERE full_name LIKE :q OR faculty_id LIKE :q ORDER BY $sort $dir LIMIT :limit OFFSET :offset");
        $s->bindValue(':q',$qLike,PDO::PARAM_STR);
        $s->bindValue(':limit',(int)$limit,PDO::PARAM_INT);
        $s->bindValue(':offset',(int)$offset,PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }

    public function countTeachers($q){
        $qLike = "%".$q."%";
        $s = $this->pdo->prepare("SELECT COUNT(*) c FROM teachers WHERE full_name LIKE :q OR faculty_id LIKE :q");
        $s->execute([':q'=>$qLike]);
        return (int)$s->fetch()['c'];
    }

    public function addTeacher($faculty_id,$full_name,$username,$sex,$email){
        $this->pdo->beginTransaction();
        $i = $this->pdo->prepare("INSERT INTO teachers(faculty_id,full_name,username,email,sex,active) VALUES(:fid,:name,:user,:email,:sex,1)");
        $i->execute([':fid'=>$faculty_id, ':name'=>$full_name, ':user'=>$username, ':email'=>$email, ':sex'=>$sex]);
        $tid = $this->pdo->lastInsertId();
        $acc = $this->pdo->prepare("INSERT INTO accounts(email,username,password_hash,role,first_login_required,person_type,person_id) VALUES(:email,:username,:ph,'teacher',1,'teacher',:pid)");
        $acc->execute([':email'=>$email, ':username'=>$username, ':ph'=>password_hash('1', PASSWORD_DEFAULT), ':pid'=>$tid]);
        $this->pdo->commit();
        return $tid;
    }

    public function updateTeacher($id,$full_name,$username,$sex,$email,$active){
        $u = $this->pdo->prepare("UPDATE teachers SET full_name=:name, username=:user, email=:email, sex=:sex, active=:active WHERE id=:id");
        $ok = $u->execute([':id'=>$id, ':name'=>$full_name, ':user'=>$username, ':email'=>$email, ':sex'=>$sex, ':active'=>(int)$active]);
        // keep accounts email in sync
        $ua = $this->pdo->prepare("UPDATE accounts SET email=:email, username=:user WHERE person_type='teacher' AND person_id=:id");
        $ua->execute([':email'=>$email, ':user'=>$username, ':id'=>$id]);
        return $ok;
    }

    public function deleteTeacher($id){
        $this->pdo->beginTransaction();
        $this->pdo->prepare("DELETE FROM accounts WHERE person_type='teacher' AND person_id=:id")->execute([':id'=>$id]);
        $ok = $this->pdo->prepare("DELETE FROM teachers WHERE id=:id")->execute([':id'=>$id]);
        $this->pdo->commit();
        return $ok;
    }

    // Students
    public function getStudents($q,$page,$limit,$sort='grade_level',$dir='ASC'){
        $offset = ($page-1)*$limit;
        $qLike = "%".$q."%";
        $allowed = ['family_name','lrn','department','grade_level','strand'];
        if(!in_array($sort,$allowed)) $sort = 'grade_level';
        $dir = strtoupper($dir)==='DESC' ? 'DESC' : 'ASC';
        $s = $this->pdo->prepare("SELECT * FROM students WHERE CONCAT(family_name,' ',first_name) LIKE :q OR lrn LIKE :q ORDER BY $sort $dir, family_name ASC LIMIT :limit OFFSET :offset");
        $s->bindValue(':q',$qLike,PDO::PARAM_STR);
        $s->bindValue(':limit',(int)$limit,PDO::PARAM_INT);
        $s->bindValue(':offset',(int)$offset,PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }

    public function countStudents($q){
        $qLike = "%".$q."%";
        $s = $this->pdo->prepare("SELECT COUNT(*) c FROM students WHERE CONCAT(family_name,' ',first_name) LIKE :q OR lrn LIKE :q");
        $s->execute([':q'=>$qLike]);
        return (int)$s->fetch()['c'];
    }

    public function getTeacherLoadsForSection($teacher_id,$section_id){
        $sql = "SELECT sl.*, s.code AS subject_code, s.name AS subject_name
                FROM subject_loads sl JOIN subjects s ON sl.subject_id=s.id
                WHERE sl.teacher_id=:tid AND sl.section_id=:sec AND sl.active=1";
        $s = $this->pdo->prepare($sql);
        $s->execute([':tid'=>$teacher_id, ':sec'=>$section_id]);
        return $s->fetchAll();
    }

    public function getAdvisoryStudentsForTeacher($teacher_id,$q,$page,$limit,$sort='family_name',$dir='ASC'){
        $offset = ($page-1)*$limit;
        // find teacher advisory section
        $t = $this->pdo->prepare("SELECT advisory_section_id FROM teachers WHERE id=:id");
        $t->execute([':id'=>$teacher_id]);
        $adv = $t->fetch();
        if(!$adv || !$adv['advisory_section_id']) return [];
        $sec = (int)$adv['advisory_section_id'];
        $qLike = "%".$q."%";
        $allowed = ['family_name','lrn','grade_level'];
        if(!in_array($sort,$allowed)) $sort = 'family_name';
        $dir = strtoupper($dir)==='DESC' ? 'DESC' : 'ASC';
        $s = $this->pdo->prepare("SELECT * FROM students WHERE advisory_section_id=:sec AND (CONCAT(family_name,' ',first_name) LIKE :q OR lrn LIKE :q) ORDER BY $sort $dir LIMIT :limit OFFSET :offset");
        $s->bindValue(':sec',$sec, PDO::PARAM_INT);
        $s->bindValue(':q',$qLike,PDO::PARAM_STR);
        $s->bindValue(':limit',(int)$limit,PDO::PARAM_INT);
        $s->bindValue(':offset',(int)$offset,PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }

    public function countAdvisoryStudentsForTeacher($teacher_id,$q){
        $t = $this->pdo->prepare("SELECT advisory_section_id FROM teachers WHERE id=:id");
        $t->execute([':id'=>$teacher_id]);
        $adv = $t->fetch();
        if(!$adv || !$adv['advisory_section_id']) return 0;
        $sec = (int)$adv['advisory_section_id'];
        $qLike = "%".$q."%";
        $c = $this->pdo->prepare("SELECT COUNT(*) c FROM students WHERE advisory_section_id=:sec AND (CONCAT(family_name,' ',first_name) LIKE :q OR lrn LIKE :q)");
        $c->execute([':sec'=>$sec, ':q'=>$qLike]);
        return (int)$c->fetch()['c'];
    }

    public function insertStudent($data){
        $sql = "INSERT INTO students(
            lrn,department,grade_level,strand,student_type,advisory_section_id,
            family_name,first_name,middle_name,suffix,birthdate,birthplace,religion,civil_status,sex,
            mobile,email,
            curr_house_street,curr_barangay,curr_city,curr_province,curr_zip,
            perm_house_street,perm_barangay,perm_city,perm_province,perm_zip,
            elem_name,elem_address,elem_year_graduated,
            last_school_name,last_school_address,
            jhs_name,jhs_address,jhs_year_graduated,
            guardian_last_name,guardian_first_name,guardian_middle_name,guardian_contact,guardian_occupation,guardian_address,guardian_relationship,
            mother_last_name,mother_first_name,mother_middle_name,mother_contact,mother_occupation,mother_address,
            father_last_name,father_first_name,father_middle_name,father_contact,father_occupation,father_address
        ) VALUES (
            :lrn,:department,:grade_level,:strand,:student_type,NULL,
            :family_name,:first_name,:middle_name,:suffix,:birthdate,:birthplace,:religion,:civil_status,:sex,
            :mobile,:email,
            :curr_house_street,:curr_barangay,:curr_city,:curr_province,:curr_zip,
            :perm_house_street,:perm_barangay,:perm_city,:perm_province,:perm_zip,
            :elem_name,:elem_address,:elem_year_graduated,
            :last_school_name,:last_school_address,
            :jhs_name,:jhs_address,:jhs_year_graduated,
            :guardian_last_name,:guardian_first_name,:guardian_middle_name,:guardian_contact,:guardian_occupation,:guardian_address,:guardian_relationship,
            :mother_last_name,:mother_first_name,:mother_middle_name,:mother_contact,:mother_occupation,:mother_address,
            :father_last_name,:father_first_name,:father_middle_name,:father_contact,:father_occupation,:father_address
        )";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return $this->pdo->lastInsertId();
    }

    public function getStudentById($id){
        $s = $this->pdo->prepare("SELECT * FROM students WHERE id = :id");
        $s->execute([':id'=>$id]);
        return $s->fetch();
    }

    public function updateStudent($id,$data){
        $sql = "UPDATE students SET
            lrn=:lrn, department=:department, grade_level=:grade_level, strand=:strand, student_type=:student_type,
            family_name=:family_name, first_name=:first_name, middle_name=:middle_name, suffix=:suffix, birthdate=:birthdate, birthplace=:birthplace, religion=:religion, civil_status=:civil_status, sex=:sex,
            mobile=:mobile, email=:email,
            curr_house_street=:curr_house_street, curr_barangay=:curr_barangay, curr_city=:curr_city, curr_province=:curr_province, curr_zip=:curr_zip,
            perm_house_street=:perm_house_street, perm_barangay=:perm_barangay, perm_city=:perm_city, perm_province=:perm_province, perm_zip=:perm_zip,
            elem_name=:elem_name, elem_address=:elem_address, elem_year_graduated=:elem_year_graduated,
            last_school_name=:last_school_name, last_school_address=:last_school_address,
            jhs_name=:jhs_name, jhs_address=:jhs_address, jhs_year_graduated=:jhs_year_graduated,
            guardian_last_name=:guardian_last_name, guardian_first_name=:guardian_first_name, guardian_middle_name=:guardian_middle_name, guardian_contact=:guardian_contact, guardian_occupation=:guardian_occupation, guardian_address=:guardian_address, guardian_relationship=:guardian_relationship,
            mother_last_name=:mother_last_name, mother_first_name=:mother_first_name, mother_middle_name=:mother_middle_name, mother_contact=:mother_contact, mother_occupation=:mother_occupation, mother_address=:mother_address,
            father_last_name=:father_last_name, father_first_name=:father_first_name, father_middle_name=:father_middle_name, father_contact=:father_contact, father_occupation=:father_occupation, father_address=:father_address
            WHERE id=:id";
        $data[':id'] = $id;
        $u = $this->pdo->prepare($sql);
        return $u->execute($data);
    }

    public function deleteStudent($id){
        $this->pdo->beginTransaction();
        $this->pdo->prepare("DELETE FROM accounts WHERE person_type='student' AND person_id=:id")->execute([':id'=>$id]);
        $ok = $this->pdo->prepare("DELETE FROM students WHERE id=:id")->execute([':id'=>$id]);
        $this->pdo->commit();
        return $ok;
    }

    public function createStudentAccount($student_id){
        $s = $this->getStudentById($student_id);
        if(!$s) return false;
        $email = "S-".$s['lrn']."@evelio.ams.edu";
        $ex = $this->pdo->prepare("SELECT id FROM accounts WHERE email=:email");
        $ex->execute([':email'=>$email]);
        if($ex->fetch()) return true;
        $u = strtolower($s['family_name']).$student_id;
        $acc = $this->pdo->prepare("INSERT INTO accounts(email,username,password_hash,role,first_login_required,person_type,person_id) VALUES(:email,:username,:ph,'student',1,'student',:pid)");
        return $acc->execute([':email'=>$email, ':username'=>$u, ':ph'=>password_hash('1', PASSWORD_DEFAULT), ':pid'=>$student_id]);
    }

    public function updateStudentAccount($student_id,$username){
        $u = $this->pdo->prepare("UPDATE accounts SET username=:u WHERE person_type='student' AND person_id=:id");
        return $u->execute([':u'=>$username, ':id'=>$student_id]);
    }

    public function getSubjectLoads($q,$page,$limit,$sort='school_year',$dir='DESC'){
        $offset = ($page-1)*$limit;
        $qLike = "%".$q."%";
        $allowed = ['school_year','semester','section_name','subject_code','subject_name','teacher_name'];
        if(!in_array($sort,$allowed)) $sort='school_year';
        $dir = strtoupper($dir)==='ASC' ? 'ASC' : 'DESC';
        $sql = "SELECT sl.*, t.full_name AS teacher_name, s.code AS subject_code, s.name AS subject_name, sec.name AS section_name
                FROM subject_loads sl
                JOIN teachers t ON sl.teacher_id=t.id
                JOIN subjects s ON sl.subject_id=s.id
                JOIN sections sec ON sl.section_id=sec.id
                WHERE t.full_name LIKE :q OR s.name LIKE :q OR sec.name LIKE :q
                ORDER BY $sort $dir, sec.name ASC
                LIMIT :limit OFFSET :offset";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(':q',$qLike,PDO::PARAM_STR);
        $st->bindValue(':limit',(int)$limit,PDO::PARAM_INT);
        $st->bindValue(':offset',(int)$offset,PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function countSubjectLoads($q){
        $qLike = "%".$q."%";
        $sql = "SELECT COUNT(*) c
                FROM subject_loads sl
                JOIN teachers t ON sl.teacher_id=t.id
                JOIN subjects s ON sl.subject_id=s.id
                JOIN sections sec ON sl.section_id=sec.id
                WHERE t.full_name LIKE :q OR s.name LIKE :q OR sec.name LIKE :q";
        $st = $this->pdo->prepare($sql);
        $st->execute([':q'=>$qLike]);
        return (int)$st->fetch()['c'];
    }

    public function addSubjectLoad($teacher_id,$subject_id,$section_id,$school_year,$semester){
        $i = $this->pdo->prepare("INSERT INTO subject_loads(teacher_id,subject_id,section_id,school_year,semester,active) VALUES(:tid,:sid,:sec,:sy,:sem,1)");
        $i->execute([':tid'=>$teacher_id, ':sid'=>$subject_id, ':sec'=>$section_id, ':sy'=>$school_year, ':sem'=>$semester?:null]);
        return $this->pdo->lastInsertId();
    }

    public function deleteSubjectLoad($id){
        $d = $this->pdo->prepare("DELETE FROM subject_loads WHERE id=:id");
        return $d->execute([':id'=>$id]);
    }

    public function getEnrollmentsByLoad($load_id){
        $sql = "SELECT e.id AS enrollment_id, st.*, e.school_year, e.semester
                FROM enrollments e
                JOIN students st ON e.student_id = st.id
                WHERE e.subject_load_id = :lid";
        $s = $this->pdo->prepare($sql);
        $s->execute([':lid'=>$load_id]);
        return $s->fetchAll();
    }

    public function getTeacherLoads($teacher_id){
        $sql = "SELECT sl.*, s.code AS subject_code, s.name AS subject_name, sec.name AS section_name
                FROM subject_loads sl
                JOIN subjects s ON sl.subject_id=s.id
                JOIN sections sec ON sl.section_id=sec.id
                WHERE sl.teacher_id = :tid AND sl.active=1";
        $s = $this->pdo->prepare($sql);
        $s->execute([':tid'=>$teacher_id]);
        return $s->fetchAll();
    }

    public function addEnrollment($student_id,$subject_load_id,$school_year,$semester){
        $i = $this->pdo->prepare("INSERT INTO enrollments(student_id,subject_load_id,school_year,semester,status) VALUES(:sid,:lid,:sy,:sem,'enrolled')");
        return $i->execute([':sid'=>$student_id, ':lid'=>$subject_load_id, ':sy'=>$school_year, ':sem'=>$semester?:null]);
    }

    public function deleteEnrollment($enrollment_id){
        $d = $this->pdo->prepare("DELETE FROM enrollments WHERE id=:id");
        return $d->execute([':id'=>$enrollment_id]);
    }

    public function getEnrollmentsByStudent($student_id){
        $sql = "SELECT e.*, s.code AS subject_code, s.name AS subject_name, sec.name AS section_name
                FROM enrollments e
                JOIN subject_loads sl ON e.subject_load_id = sl.id
                JOIN subjects s ON sl.subject_id = s.id
                JOIN sections sec ON sl.section_id = sec.id
                WHERE e.student_id = :sid";
        $s = $this->pdo->prepare($sql);
        $s->execute([':sid'=>$student_id]);
        return $s->fetchAll();
    }

    public function upsertGrade($enrollment_id,$grade){
        $sel = $this->pdo->prepare("SELECT id FROM grades WHERE enrollment_id=:eid");
        $sel->execute([':eid'=>$enrollment_id]);
        if($sel->fetch()){
            $u = $this->pdo->prepare("UPDATE grades SET grade=:g WHERE enrollment_id=:eid");
            return $u->execute([':g'=>$grade, ':eid'=>$enrollment_id]);
        }else{
            $i = $this->pdo->prepare("INSERT INTO grades(enrollment_id,grade) VALUES(:eid,:g)");
            return $i->execute([':eid'=>$enrollment_id, ':g'=>$grade]);
        }
    }

    public function getGradesForLoad($load_id){
        $sql = "SELECT g.enrollment_id, g.grade
                FROM grades g
                JOIN enrollments e ON g.enrollment_id = e.id
                WHERE e.subject_load_id = :lid";
        $s = $this->pdo->prepare($sql);
        $s->execute([':lid'=>$load_id]);
        return $s->fetchAll();
    }

    public function markAttendance($student_id,$subject_load_id,$date,$status){
        $sel = $this->pdo->prepare("SELECT id FROM attendance WHERE student_id=:sid AND subject_load_id=:lid AND date=:d");
        $sel->execute([':sid'=>$student_id, ':lid'=>$subject_load_id, ':d'=>$date]);
        if($sel->fetch()){
            $u = $this->pdo->prepare("UPDATE attendance SET status=:st WHERE student_id=:sid AND subject_load_id=:lid AND date=:d");
            return $u->execute([':st'=>$status, ':sid'=>$student_id, ':lid'=>$subject_load_id, ':d'=>$date]);
        }else{
            $i = $this->pdo->prepare("INSERT INTO attendance(student_id,subject_load_id,date,status) VALUES(:sid,:lid,:d,:st)");
            return $i->execute([':sid'=>$student_id, ':lid'=>$subject_load_id, ':d'=>$date, ':st'=>$status]);
        }
    }

    public function getAttendanceForLoadAndDate($subject_load_id,$date){
        $sql = "SELECT student_id, status FROM attendance WHERE subject_load_id=:lid AND date=:d";
        $s = $this->pdo->prepare($sql);
        $s->execute([':lid'=>$subject_load_id, ':d'=>$date]);
        return $s->fetchAll();
    }

    public function getAttendanceByStudent($student_id,$year,$month){
        $sql = "SELECT a.*, s.name AS subject_name
                FROM attendance a
                JOIN subject_loads sl ON a.subject_load_id=sl.id
                JOIN subjects s ON sl.subject_id=s.id
                WHERE a.student_id=:sid AND YEAR(a.date)=:y AND MONTH(a.date)=:m";
        $s = $this->pdo->prepare($sql);
        $s->execute([':sid'=>$student_id, ':y'=>$year, ':m'=>$month]);
        return $s->fetchAll();
    }

    public function assignStudentSection($student_id,$section_id){
        $cap = $this->pdo->prepare("SELECT capacity FROM sections WHERE id=:id");
        $cap->execute([':id'=>$section_id]);
        $row = $cap->fetch();
        if(!$row) return false;
        $capacity = (int)$row['capacity'];
        $count = $this->pdo->prepare("SELECT COUNT(*) c FROM students WHERE advisory_section_id=:id");
        $count->execute([':id'=>$section_id]);
        $current = (int)$count->fetch()['c'];
        if($current >= $capacity) return false;
        $u = $this->pdo->prepare("UPDATE students SET advisory_section_id=:sec WHERE id=:id");
        return $u->execute([':sec'=>$section_id, ':id'=>$student_id]);
    }
}
?>
