<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Types CRUD</title>
    <style>
        a {
            text-decoration: none !important;
            color: black !important;
        }

        a:hover {
            color: blueviolet !important;
        }

    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
            crossorigin="anonymous"></script>
    <script src="/src/index.js"></script>
</head>
<body>
<div class="d-flex flex-row justify-content-between" style="height:100vh; width:100vw; overflow: hidden;">
    <div class="col-2 pt-3 ps-3 me-3 border rounded">
        <div class="header">
            <h1>Vehicle Types Manager</h1>
            <a class="fs-3" href="/">Тип транспорту</a>
        </div>
    </div>
    <div class="col-9">
        <div class="row d-flex justify-content-end border rounded py-3 text-center align-content-center">
            <h3 class="col-2"><?= $_SESSION['username'] ?></h3>
            <a class="col-2 align-content-center" href="/logout/">Вийти з аккаунту</a>
        </div>

        <div class="row border rounded mt-3 py-3">
            <div id="messageContainer"></div>