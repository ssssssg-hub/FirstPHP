<?php
//    $pg_db_host = 'localhost';
//    $pg_db_username = 'postgres'; // 자신이 설정한 DB 유저(role)이름. postgres는 기본 이름
//    $pg_db_name = 'postgres'; // 자신이 만든 DB 이름, postgres는 기본 DB이름
//    $pg_db_password = 'password'; // 자신이 설정한 비밀번호 입력
//    $dsn = "pgsql:host=$pg_db_host;dbname=$pg_db_name";
//    $pdo = new PDO($dsn, $pg_db_username, $pg_db_password); // PDO 객체 생성.

/*
 *      PDO(PHP Data Objects) = 같은 방법으로 여러 데이터베이스에 접근할 수 있게 해주는 PHP 확장 모듈
 *      PDO 사용으로 MySQL, ORACLE, MS SQL, PostgreSQL 등등 여러 데이터베이스를 같은 방식으로 다룰 수 있게 된다.
 *
 *      PDO를 사용하면 준비 구문(prepare statement)을 활용할 수 있다.
 *      준비 구문을 사용하면 SQL 인젝션 공격을 막을 수 있고, 애플리케이션의 성능이 향상된다.
 *      SQL 인젝션 취약점은 사용자 입력값과 함께 동적으로 쿼리를 만들 때 발생한다.
 *      SQL 인젝션을 쉽게 얘기하면 쿼리 문법과 데이터를 구분하지 않고 한번에 쿼리문을 날리는 것.
 *
 *      준비 구문을 이용할 때는 우선 SQL 코드를 정의하고 나중에 파라미터를 대입한다.
 *
 *      데이터베이스가 SQL 코드와 데이터를 명확히 구분할 수 있기 때문에, 준비 구문을 쓰는 것만으로도 SQL 인젝션 공격이 막힌다.
 *      준비 구문을 만들고 값만 바꿔가며 여러 번 실행하는 경우, 클라이언트와 서버 양쪽에서 쿼리 계획과 메타 정보를 캐시할 수 있게 되어 애플리케이션 성능이 향상된다.
 * */
$dbconn = new PDO("pgsql:host=117.52.153.104;dbname=edu", "edu", "zjajtmfoq");
?>