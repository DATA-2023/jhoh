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
		input[type='radio'] {
			transform: scale(1.3); /* 라디오 버튼 크기 키우기 */
			margin-right: px; /* 간격 조정 */
		}
	
		/* 라디오 버튼 옆 텍스트 크기 키우기 */
		label {
			font-size: 16px;
		}
    </style>
</head>
<body>
    <header>
        <h1>서울시 공공자전거 이용정보 보고서</h1>
    </header>
    <nav>
        <!-- 자기 파트 웹페이지 만들어서 제목 추하 html파일 연결하기-->
        <a href="#fix_avg">고장수리 평균 소요시간</a>
    </nav>
    <div class="container">
        <h2>고장사유별 수리 평균 소요시간</h2>
		<!-- 앞에꺼랑 같이 붙일때 주석 제거하기
		<canvas id="fix_avg"></canvas> -->
        <p>서울시 공공자전거를 이용하는 회원들이 접수한 자전거 고장내역 중  "사유별 수리 평균 소요시간" 에 대한 정보입니다.<br> </p>
     

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

// Get the submitted reason or default to an empty string
$selected_reason = isset($_GET['reason']) ? $_GET['reason'] : '';

// HTML form
echo "<form action='' method='GET'>";
echo '고장 사유 선택: ';
$reasons = ['안장 ', '페달 ', '타이어 ', '단말기 ', '체인 ', '기타      '];
foreach ($reasons as $reason) {
    echo "<label><input type='radio' name='reason' value='$reason'";
    if ($reason === $selected_reason) {
        echo ' checked';
    }
    echo ">$reason</label>";
}
echo '<input type="submit" name="submit" value="조회">';
echo '</form>';

// When the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['reason'])) {
    $selected_reason = $_GET['reason'];

    // SQL query creation
    $sql = "CREATE VIEW avg_time AS (
        SELECT
            t1.complaint_id,
            t1.bike_id,
            t1.datetime,
            t1.reason_to_fix,
            t2.return_datetime,
            TIMEDIFF(t2.return_datetime, t1.datetime) AS interval_duration
        FROM
            bike_breakdown t1
        JOIN
            bike_fix t2 ON t1.complaint_id = t2.complaint_id AND t1.bike_id = t2.bike_id
    );";

    $sql1 = "SELECT ROUND(AVG(TIME_TO_SEC(interval_duration) / 3600), 2) AS avg_duration FROM avg_time WHERE reason_to_fix='$selected_reason'";

	$sql3 = "SELECT COUNT(*) AS count_num FROM avg_time WHERE reason_to_fix = '$selected_reason'";
    
    $sql2 = "DROP VIEW avg_time";

    // Execute SQL queries
    $result1 = $conn->query($sql);
    $result2 = $conn->query($sql1);
	$result3 = $conn->query($sql3);

    // Display results
	if ($result3->num_rows > 0) {
        $row = $result3->fetch_assoc();
		echo '<br><label>' . $selected_reason . '</label>';
		echo '<label> 수리 총 요청 건수: ' . $row['count_num'] . '건</label>';
		}
        

    if ($result2->num_rows > 0) {
        $row = $result2->fetch_assoc();

		echo '<br><label>' . $selected_reason . '</label>';
        echo '<label> 수리 평균 소요시간: ' . $row['avg_duration'] . '시간</label>';
    }   

	else {
        echo '<label>데이터를 찾을 수 없습니다.</label>';
    }

    // Drop the view
    $result3 = $conn->query($sql2);
}

// Close database connection
$conn->close();
?>

    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        

    </script>
</body>
</html>
