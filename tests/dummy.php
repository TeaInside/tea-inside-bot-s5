<?php

$argv[1] = '{
    "message_id": 127248,
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
    "date": 1546223988,
    "new_chat_participant": {
        "id": 243692601,
        "is_bot": false,
        "first_name": "Ammar",
        "last_name": "Faizi",
        "username": "ammarfaizi2",
        "language_code": "en"
    },
    "new_chat_member": {
        "id": 243692601,
        "is_bot": false,
        "first_name": "Ammar",
        "last_name": "Faizi",
        "username": "ammarfaizi2",
        "language_code": "en"
    },
    "new_chat_members": [
        {
            "id": 243692601,
            "is_bot": false,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "language_code": "en"
        }
    ]
}';

require __DIR__."/../connectors/telegram/webhook_worker.php";
// require __DIR__."/../connectors/telegram/logger.php";
