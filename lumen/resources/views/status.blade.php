<html>

<head>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            text-align: center;
        }
    </style>
</head>
<body>


<h3>接收到的通知:</h3>

<p> 当前页: {{ $current_page }} ; 总页数: {{ $last_page }} </p>

<br>

<table>
    <tr>
        <th>索引</th>
        <th>用户ID</th>
        <th>流水号</th>
        <th>通知类型</th>
        <th>通知内容</th>
        <th>创建时间</th>
        <th>修改时间</th>
    </tr>
    @foreach ($notice as $row)
        <tr>
            <td>{{ $row['id'] }}</td>
            <td>{{ $row['uid'] }}</td>
            <td>{{ $row['ref'] }}</td>
            <td>{{ $row['type'] }}</td>
            <td>{{ var_dump(json_decode($row['result'], true), true) }}</td>
            <td>{{ $row['created_at'] }}</td>
            <td>{{ $row['updated_at'] }}</td>
        </tr>
    @endforeach
</table>

</body>
</html>