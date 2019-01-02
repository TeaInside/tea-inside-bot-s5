<?php

$argv[1] = '{
    "update_id": 345146029,
    "message": {
        "message_id": 129569,
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
            "type": "supergroup"
        },
        "date": 1546439198,
        "reply_to_message": {
            "message_id": 129512,
            "from": {
                "id": 742278412,
                "is_bot": false,
                "first_name": "moe@moepoi.jp",
                "username": "moepoi"
            },
            "chat": {
                "id": -1001162202776,
                "title": "Koding Teh",
                "type": "supergroup"
            },
            "date": 1546438370,
            "photo": [
                {
                    "file_id": "AgADBQADnagxG1jKaFVYbMrDYs7veZhq3jIABJsGcTGBwjKsesYBAAEC",
                    "file_size": 1447,
                    "width": 90,
                    "height": 56
                },
                {
                    "file_id": "AgADBQADnagxG1jKaFVYbMrDYs7veZhq3jIABR3e5POgJnd7xgEAAQI",
                    "file_size": 17580,
                    "width": 320,
                    "height": 200
                },
                {
                    "file_id": "AgADBQADnagxG1jKaFVYbMrDYs7veZhq3jIABMZyeBzNPwFTfMYBAAEC",
                    "file_size": 44187,
                    "width": 640,
                    "height": 400
                }
            ]
        },
        "text": "/whatanime",
        "entities": [
            {
                "offset": 0,
                "length": 6,
                "type": "bot_command"
            }
        ]
    }
}';

require __DIR__."/../connectors/telegram/webhook_worker.php";
// require __DIR__."/../connectors/telegram/logger.php";
