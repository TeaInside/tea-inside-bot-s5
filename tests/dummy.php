<?php

$argv[1] = '{
    "update_id": 345132986,
    "message": {
        "message_id": 122867,
        "from": {
            "id": 243692601,
            "is_bot": false,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "language_code": "en"
        },
        "chat": {
            "id": -1001162202776,
            "title": "Koding Teh",
            "username": "KodingTeh",
            "type": "supergroup"
        },
        "date": 1545485003,
        "photo": [
            {
                "file_id": "AgADBQADb6gxGzyZ8FS8GymLZG343IZs3jIABM4zF7W9bKLlaowBAAEC",
                "file_size": 623,
                "width": 90,
                "height": 28
            },
            {
                "file_id": "AgADBQADb6gxGzyZ8FS8GymLZG343IZs3jIABOmIsj20eLXXa4wBAAEC",
                "file_size": 7756,
                "width": 320,
                "height": 99
            },
            {
                "file_id": "AgADBQADb6gxGzyZ8FS8GymLZG343IZs3jIABLrJab4iSCvrbIwBAAEC",
                "file_size": 32194,
                "width": 800,
                "height": 248
            },
            {
                "file_id": "AgADBQADb6gxGzyZ8FS8GymLZG343IZs3jIABKC6LlKKzPr-aYwBAAEC",
                "file_size": 65125,
                "width": 1240,
                "height": 384
            }
        ],
        "caption": "Udah masuk"
    }
}';

// require __DIR__."/../connectors/telegram/webhook_worker.php";
require __DIR__."/../connectors/telegram/logger.php";
