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
        <th>id</th>
        <th>uid</th>
        <th>ref</th>
        <th>result</th>
        <th>created_at</th>
        <th>updated_at</th>
    </tr>
    @foreach ($notice as $row)
        <tr>
            <td>{{ $row['id'] }}</td>
            <td>{{ $row['uid'] }}</td>
            <td>{{ $row['ref'] }}</td>
            <td>{{ $row['result'] }}</td>
            <td>{{ $row['created_at'] }}</td>
            <td>{{ $row['updated_at'] }}</td>
        </tr>
    @endforeach
</table>

</body>
</html>