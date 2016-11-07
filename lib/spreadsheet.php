<?php
require '/srv/http/lib/functions.php';
$db = get_db();
$user = get_cur_user();
if (!$user) { header('Location: https://members.darwinsdogs.org'); die(); }

$query_data_dump = '
SELECT name, questions.string as question,
substring_index(substring_index(options,"|",answer + 1),"|",-1) AS answer, notes
FROM dogs, questions, answers, formats
WHERE dogs.owner = :id
	AND answers.dog = dogs.id
	AND questions.format = formats.id
	AND answers.question = questions.id
	AND questions.format IN (1, 2, 7, 8, 10, 12, 13, 14, 15)
UNION
SELECT name, questions.string as question, answer, notes
FROM dogs, questions, answers, formats
WHERE dogs.owner = :id
	AND answers.dog = dogs.id
	AND questions.format = formats.id
	AND answers.question = questions.id
	AND questions.format NOT IN (1, 2, 7, 8, 10, 12, 13, 14, 15)';

$stmt = $db->prepare($query_data_dump);
$stmt->bindValue(':id', $user['id'], PDO::PARAM_INT);
if (!$stmt->execute()) die('Query Error');
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=darwinsdogs_data.csv');
echo 'Dog,Question,Answer,Notes', PHP_EOL;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	echo $row['name'], ',', $row['question'], ',', $row['answer'], ',', $row['notes'], PHP_EOL;
?>

