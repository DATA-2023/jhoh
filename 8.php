<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <title>서울시 공공자전거 이용정보 보고서</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        header {
            background-color: #0078d4;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        h1 {
            font-size: 24px;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        h2 {
            font-size: 20px;
            margin-top: 20px;
        }
        p {
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #0078d4;
            color: #fff;
        }
        /* 네비게이션바 스타일 */
        nav {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin: 10px;
        }

    </style>
</head>
<body>
    <header>
        <h1>서울시 공공자전거 이용정보 보고서</h1>
    </header>
    <nav>
        <!-- 자기 파트 웹페이지 만들어서 제목 추하 html파일 연결하기-->
        <a href="#ranking">주차별 따릉이 최다 이용자 순위</a>
    </nav>
    <div class="container">
        <h2>주차별 따릉이 최다 이용자 순위</h2>
		<!-- 앞에꺼랑 같이 붙일때 주석 제거하기
		<canvas id="ranking"></canvas> -->
        <p>주차별 거리를 기준으로 한 따릉이 최다 이용자 순위에 대한 정보입니다.<br> </p>
     
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
  Enter week number: <input type="number" name="user_input">
  <input type="submit">
</form>
        <?php
            $host="localhost";
            $user="root";
            $pw="DS.UpGNIk4[e(e2s";
            $dbName="bicycle_db";

            // MySQL 연결
            $conn = new mysqli($host, $user, $pw, $dbName);

			

// Connection check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
	
	$user_input = $_POST['user_input'];
    	
	// MySQL 쿼리 실행
    $sql = "SELECT user_id, gender, age_group, total_distance
            FROM (
                SELECT u.user_id, u.gender, u.age_group, 
                    SUM(wu.moving_distance) AS total_distance,
                    TIMESTAMPDIFF(WEEK, '2023-06-01', rh.rent_datetime) + 1 AS week_number,
                    RANK() OVER (ORDER BY SUM(wu.moving_distance) DESC) AS weekly_rank
                FROM user u
                JOIN usage_per_user upu ON u.user_id = upu.user_id
                JOIN workout_usage wu ON upu.usage_id = wu.usage_id
                JOIN rent_history rh ON upu.usage_id = rh.usage_id
                WHERE TIMESTAMPDIFF(WEEK, '2023-06-01', rh.rent_datetime) + 1 = ?
                GROUP BY u.user_id, u.gender, u.age_group, TIMESTAMPDIFF(WEEK, '2023-06-01', rh.rent_datetime)
            ) AS TotalDistancePerUser
            WHERE weekly_rank <= 10
            ORDER BY total_distance DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_input); // 정수형 파라미터를 바인딩
    $stmt->execute();
    $result = $stmt->get_result();

    // 쿼리 결과 출력
    if ($result->num_rows > 0) {
        echo '<br>6월 <label>' . $user_input . '</label>';
        echo "주차 따릉이 최다 이용자 순위<br>";
		echo "<br><table border='1'><tr><th>User ID</th><th> Gender </th><th>Age Group</th><th>Total Distance </th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["user_id"]. "</td><td>" . $row["gender"]. "</td><td>" . $row["age_group"]. "</td><td>" . $row["total_distance"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<br>결과가 없습니다.";
    }

    // 연결 종료
    $stmt->close();
    $conn->close();
	
?>

    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        

    </script>
</body>
</html>
