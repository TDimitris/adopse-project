<?php

function emailExists(String $email,String $genError) 
    {
        $GLOBALS['temp2'] = $email;

        try
            {
                $q = "SELECT 1 FROM users WHERE email=?";
                $stmt = $GLOBALS['conn']->prepare($q);
                $stmt->execute([$email]);
                $_SESSION['exec_reset'] = 0;
                $GLOBALS['temp3'] = 1;
                return $stmt->fetchColumn();

            } 
        catch (Exception $ex) {$genError = $ex;}
    }
    
function PasswordIsAuthenticated(String $password, $genError, String $email) 
    {
        try
            {
                $q = "SELECT password FROM users WHERE email=?";
                $stmt = $GLOBALS['conn']->prepare($q);
                $stmt->execute([$email]);
                $_SESSION['exec_reset'] = 0;
                $GLOBALS['temp3'] = 1;
                $temp = $stmt->fetchColumn();
                if(strcmp($password,$temp))
                    {
                        return false;
                    }
                else
                    {
                        return true;
                    }
            } 
        catch (Exception $ex) {$genError = $ex;}
    }
    
function selectMaxFromCreator($id,$table)
    {
        $q = "SELECT MAX(id) as mid FROM $table WHERE idcreator=? LIMIT 1";
            $stmt = $GLOBALS['conn']->prepare($q);
            $stmt->execute([$id]);
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            return $results["mid"];
    }

    
function selectQuestionAnswers($qid)
    {
        $q = "SELECT id, text, correct, parent FROM answers WHERE parent=?";
        $stmt = $GLOBALS['conn']->prepare($q);
        $stmt->execute([$qid]);
        $results = $stmt->fetchAll();
        return $results;
    }

function selectQuestionsNotAlreadyInCurrentQuiz($qid)
    {
        $select = "
            select distinct * 
            from questions 
            where id 
            in 
                (
                    select distinct questionid 
                    from quizquestions 
                    where questionid 
                    not in 
                        (
                            select questionid 
                            from quizquestions 
                            where quizid=?
                        )
                    );";
        $stmt1 = $GLOBALS['conn']->prepare($select);
        $stmt1->execute([$qid]);
        return $stmt1->fetchAll();
    }

function selectQuestionsAlreadyInCurrentQuiz($qid)
    {
        $select = "	
            select * 
            from questions
            where id 
            in 
                (
                    SELECT questionid 
                    FROM  quizquestions 
                    where quizid=?
                );";
        $stmt1 = $GLOBALS['conn']->prepare($select);
        $stmt1->execute([$qid]);
        return $stmt1->fetchAll();
    }

function selectAllUserQuestions($cid)
{
    $select = "	
            select * 
            from questions
            where idcreator=? order by id desc;";
    $stmt1 = $GLOBALS['conn']->prepare($select);
    $stmt1->execute([$cid]);
    return $stmt1->fetchAll();
}


function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>