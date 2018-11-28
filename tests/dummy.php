<?php

$argv[1] = '{
    "update_id": 345083769,
    "message": {
        "message_id": 201576,
        "from": {
            "id": 243692601,
            "is_bot": false,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": -1001134152012,
            "title": "Berlatih dan Testing Bot",
            "username": "berlatihbot",
            "type": "supergroup"
        },
        "date": 1542300037,
        "text": "<?java public class test { public static void main(String[] argv) { System.out.println(\"Hello World!\"); } }",
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
