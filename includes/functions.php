<?php

function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone)
{
    return preg_match('/^\d{11}$/', $phone);
}

function isEmailUnique($pdo, $email, $id = null)
{
    $query = 'SELECT COUNT(*) FROM contacts WHERE email = :email';
    $params = [':email' => $email];

    if ($id) {
        $query .= ' AND id != :id';
        $params[':id'] = $id;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchColumn() == 0;
}

function isPhoneUnique($pdo, $phone, $id = null)
{
    $query = 'SELECT COUNT(*) FROM contacts WHERE phone_number = :phone';
    $params = [':phone' => $phone];

    if ($id) {
        $query .= ' AND id != :id';
        $params[':id'] = $id;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchColumn() == 0;
}

