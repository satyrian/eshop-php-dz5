<?php
if (!$comments["quantity"])
    echo "<p>Комментариев пока нет. Будь первым :)</p>";
else {
    echo "<p>Всего комментариев: {$comments["quantity"]}</p>";
    array_pop($comments);
    foreach ($comments as $comment) {
        $dt = date("d-m-Y в H:i", $comment["dt"]);
        $msg = nl2br($comment["comment"]);
        echo <<<MSG
            <p>
            <a href='mailto:{$comment["email"]}'>{$comment["name"]}</a>
            $dt написал <br/> $msg
            </p>
MSG;
    }
}
