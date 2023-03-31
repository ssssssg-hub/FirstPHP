<?php
//    $pg_db_host = 'localhost';
//    $pg_db_username = 'postgres'; // 자신이 설정한 DB 유저(role)이름. postgres는 기본 이름
//    $pg_db_name = 'postgres'; // 자신이 만든 DB 이름, postgres는 기본 DB이름
//    $pg_db_password = 'password'; // 자신이 설정한 비밀번호 입력
//    $dsn = "pgsql:host=$pg_db_host;dbname=$pg_db_name";
//    $pdo = new PDO($dsn, $pg_db_username, $pg_db_password); // PDO 객체 생성.
$dbconn = new PDO("pgsql:host=117.52.153.104;dbname=edu", "edu", "zjajtmfoq");
?>