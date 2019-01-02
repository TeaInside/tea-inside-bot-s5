<?php

$argv[1] = '{
    "update_id": 345145873,
    "message": {
        "message_id": 129416,
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
        "date": 1546435835,
        "reply_to_message": {
            "message_id": 129415,
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
            "date": 1546435831,
            "photo": [
                {
                    "file_id": "AgADBQADb6gxG8HvaFXHVcS1WxE_Nubt3zIABLPBMrrRAiUMjzkAAgI",
                    "file_size": 1000,
                    "width": 90,
                    "height": 51
                },
                {
                    "file_id": "AgADBQADb6gxG8HvaFXHVcS1WxE_Nubt3zIABPhyLgMJSd7MkDkAAgI",
                    "file_size": 10711,
                    "width": 320,
                    "height": 180
                },
                {
                    "file_id": "AgADBQADb6gxG8HvaFXHVcS1WxE_Nubt3zIABDuFyfCz3ndEkTkAAgI",
                    "file_size": 39543,
                    "width": 800,
                    "height": 450
                },
                {
                    "file_id": "AgADBQADb6gxG8HvaFXHVcS1WxE_Nubt3zIABDq9YW7lPnzWjjkAAgI",
                    "file_size": 54085,
                    "width": 1280,
                    "height": 720
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
