const fs = require('fs');
const bcrypt = require('bcryptjs');

const seederContent = fs.readFileSync('database/seeders/WaliSantriSeeder.php', 'utf8');

// Use regex to carefully extract the arrays
const studentsMatch = seederContent.match(/\$students\s*=\s*\[([\s\S]*?)\];/);
const familiesMatch = seederContent.match(/\$families\s*=\s*\[([\s\S]*?)\];/);

if (!studentsMatch || !familiesMatch) {
    console.error("Could not find arrays!");
    process.exit(1);
}

// Convert PHP array strings to JS evaluable strings
function phpArrayToJs(phpString) {
    let jsString = phpString
        .replace(/\[/g, '[')
        .replace(/\]/g, ']')
        // Strip out the wrapping brackets from match
        .trim();
        
    // We will parse it line by line
    let rows = jsString.split('\n').filter(l => l.trim().startsWith('['));
    let parsed = rows.map(r => {
        let clean = r.trim();
        if (clean.endsWith(',')) clean = clean.slice(0, -1); // remove trailing comma
        return eval('(' + clean.replace(/null/g, 'null') + ')');
    });
    return parsed;
}

const students = phpArrayToJs(studentsMatch[1]);
const families = phpArrayToJs(familiesMatch[1]);

let sql = `
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE parent_student;
TRUNCATE TABLE parents;
DELETE FROM users WHERE role_id = (SELECT id FROM roles WHERE name = 'wali_santri');
TRUNCATE TABLE students;
TRUNCATE TABLE classes;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Insert Classes
INSERT INTO classes (id, name, level, capacity, is_active, created_at, updated_at) VALUES 
(1, 'Mustawa 1 Ikhwan', 1, 25, 1, NOW(), NOW()),
(2, 'Mustawa 1 Akhwat', 1, 25, 1, NOW(), NOW()),
(3, 'Mustawa 2 Ikhwan', 2, 25, 1, NOW(), NOW()),
(4, 'Mustawa 2 Akhwat', 2, 25, 1, NOW(), NOW()),
(5, 'Mustawa 3 Ikhwan', 3, 25, 1, NOW(), NOW()),
(6, 'Mustawa 3 Akhwat', 3, 25, 1, NOW(), NOW()),
(7, 'Mustawa 4 Ikhwan', 4, 25, 1, NOW(), NOW()),
(8, 'Mustawa 4 Akhwat', 4, 25, 1, NOW(), NOW()),
(9, 'Mustawa 5 Ikhwan', 5, 25, 1, NOW(), NOW()),
(10, 'Mustawa 5 Akhwat', 5, 25, 1, NOW(), NOW()),
(11, 'Mustawa 6 Ikhwan', 6, 25, 1, NOW(), NOW()),
(12, 'Mustawa 6 Akhwat', 6, 25, 1, NOW(), NOW());

`;

const classMap = {
    'Mustawa 1 Ikhwan': 1, 'Mustawa 1 Akhwat': 2,
    'Mustawa 2 Ikhwan': 3, 'Mustawa 2 Akhwat': 4,
    'Mustawa 3 Ikhwan': 5, 'Mustawa 3 Akhwat': 6,
    'Mustawa 4 Ikhwan': 7, 'Mustawa 4 Akhwat': 8,
    'Mustawa 5 Ikhwan': 9, 'Mustawa 5 Akhwat': 10,
    'Mustawa 6 Ikhwan': 11, 'Mustawa 6 Akhwat': 12
};

let studentSql = 'INSERT INTO students (id, nis, class_id, name, gender, birth_place, birth_date, address, guardian_name, is_active, created_at, updated_at) VALUES \n';
let stuId = 1;
let stuMap = {}; // NIS -> ID

students.forEach((s, idx) => {
    let [nis, nama, kelas, gender, tLahir, tglLahir, alamat, namaWali] = s;
    let clsId = classMap[kelas] || 'NULL';
    stuMap[nis] = stuId;
    let w = namaWali !== null ? "'" + namaWali.replace(/'/g, "''") + "'" : 'NULL';
    let end = idx === students.length - 1 ? ';' : ',';
    studentSql += '(' + stuId + ", '" + nis + "', " + clsId + ", '" + nama.replace(/'/g, "''") + "', '" + gender + "', '" + tLahir.replace(/'/g, "''") + "', '" + tglLahir + "', '" + alamat.replace(/'/g, "''") + "', " + w + ", 1, NOW(), NOW())" + end + '\n';
    stuId++;
});
sql += studentSql + "\n\n";

let userSqlList = [];
let parentSqlList = [];
let pivotSqlList = [];
let userId = 100;
let parentId = 100;

let emails = new Set();

function esc(str) {
    if (str === null || str === undefined || str === '-') return 'NULL';
    return "'" + String(str).replace(/'/g, "''") + "'";
}

function sanitizePhone(str) {
    if (str === null || str === undefined || str === '-') return 'NULL';
    let s = String(str).replace(/'/g, "''").substring(0, 15);
    return "'" + s + "'";
}

// Fixed bcypy salt for speed since this is fake/local generation. 
// Wait, we need actual passwords. To speed up, we pre-hash known default passwords, or use sync.
console.log("Generating hashes for families... this might take 10 seconds.");

families.forEach((f) => {
    let [nAyah, nikAyah, hpAyah, pekAyah, emailAyah, nIbu, nikIbu, hpIbu, pekIbu, emailIbu, alamat, childrenNis] = f;
    const nisForUsn = childrenNis[0];
    
    // Process child mapping
    let childIds = childrenNis.map(n => stuMap[n]).filter(Boolean);

    // Ayah
    if (nAyah && nAyah !== '-' && nikAyah !== 'tidak tahu' && nikAyah) {
        let usn = 'A' + nisForUsn;
        let em = emailAyah && emailAyah !== '-' ? emailAyah.trim() : usn + '@kajian.griyaquran.web.id';
        while (emails.has(em)) { em = usn + Date.now() + '@kajian.griyaquran.web.id'; }
        emails.add(em);

        const hash = bcrypt.hashSync(usn, 10);
        userSqlList.push('(' + userId + ', ' + esc(nAyah) + ", '" + usn + "', '" + em + "', '" + hash + "', (SELECT id FROM roles WHERE name='wali_santri'), " + sanitizePhone(hpAyah) + ", 1, NOW(), NOW())");
        parentSqlList.push('(' + parentId + ', ' + userId + ", 'father', " + esc(nikAyah) + ", " + esc(pekAyah) + ", " + esc(alamat) + ", '" + usn + "', NOW(), NOW())");
        
        childIds.forEach(cid => {
            pivotSqlList.push('(' + parentId + ', ' + cid + ", 'biological', 1)");
        });
        userId++; parentId++;
    }

    // Ibu
    if (nIbu && nIbu !== '-') {
        let usn = 'B' + nisForUsn;
        let em = emailIbu && emailIbu !== '-' ? emailIbu.trim() : usn + '@kajian.griyaquran.web.id';
        while (emails.has(em)) { em = usn + Date.now() + '@kajian.griyaquran.web.id'; }
        emails.add(em);

        const hash = bcrypt.hashSync(usn, 10);
        userSqlList.push('(' + userId + ', ' + esc(nIbu) + ", '" + usn + "', '" + em + "', '" + hash + "', (SELECT id FROM roles WHERE name='wali_santri'), " + sanitizePhone(hpIbu) + ", 1, NOW(), NOW())");
        parentSqlList.push('(' + parentId + ', ' + userId + ", 'mother', " + esc(nikIbu) + ", " + esc(pekIbu) + ", " + esc(alamat) + ", '" + usn + "', NOW(), NOW())");
        
        childIds.forEach(cid => {
            pivotSqlList.push('(' + parentId + ', ' + cid + ", 'biological', 0)");
        });
        userId++; parentId++;
    }
});

let _userChunks = [];
while(userSqlList.length) _userChunks.push(userSqlList.splice(0, 100));

_userChunks.forEach(chunk => {
    sql += 'INSERT INTO users (id, name, username, email, password, role_id, phone, is_active, created_at, updated_at) VALUES \n' + chunk.join(",\n") + ';\n';
});

sql += '\n';

let _parentChunks = [];
while(parentSqlList.length) _parentChunks.push(parentSqlList.splice(0, 100));

_parentChunks.forEach(chunk => {
    sql += 'INSERT INTO parents (id, user_id, type, nik, occupation, address, qr_code_string, created_at, updated_at) VALUES \n' + chunk.join(",\n") + ';\n';
});

sql += '\n';

let _pivotChunks = [];
while(pivotSqlList.length) _pivotChunks.push(pivotSqlList.splice(0, 100));

_pivotChunks.forEach(chunk => {
    sql += 'INSERT INTO parent_student (parent_id, student_id, relationship, is_primary_contact) VALUES \n' + chunk.join(",\n") + ';\n';
});

fs.writeFileSync('import_walsan_final.sql', sql);
console.log('SUCCESS! Saved to import_walsan_final.sql');
