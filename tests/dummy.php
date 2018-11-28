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
        "text": "<?asm\nsection .data\n  a11 db 32\nsection .bss\n  z11 resb 3\nsection .text\n  global _start\n\n_start:\n  mov rax,200\n  call itoa\n  mov rax,60\n  xor rdi,rdi\n  syscall\n\nitoa:\n  mov r14,1\n  mov rbx,rax\n  mov r15,0\n  mov r8,48\n  mov [z11],r8\n  call rnd\n  ret\n\nrnd:\n  call pz11\n  call pbl\n  inc r15\n  cmp r15,100\n  jge pgg2\n  cmp r15,10\n  jge pgg\n  cmp r15,10\n  jl pll\n  ret\n\npgg:\n  mov r8,r15\n  mov rax,r8\n  mov r9,10\n  xor rdx,rdx\n  idiv r9\n  add rax,48\n  add rdx,48\n  mov [z11],rax\n  mov [z11+1],rdx\n  mov r14,2\n  cmp r15,rbx\n  jne rnd\n  ret\n\npgg2:\n  mov r8,r15\n  mov rax,r8\n  mov r9,100\n  xor rdx,rdx\n  idiv r9\n  add rax,48\n  mov [z11],rax\n  cmp rdx,10\n  jl pgg21\n  cmp rdx,10\n  jge pgg22\n  ret\npgg21:\n  mov r8,48\n  mov [z11+1],r8\n  mov r8,rdx\n  add r8,48\n  mov [z11+2],r8\n  mov r14,3\n  jmp epgg\n  ret\npgg22:\n  mov rax,rdx\n  xor rdx,rdx\n  mov r8,10\n  idiv r8\n  add rax,48\n  mov [z11+1],rax\n  mov r8,rdx\n  add r8,48\n  mov [z11+2],r8\n  mov r14,3\n  jmp epgg\n  ret\nepgg:\n  cmp r15,rbx\n  jng rnd\n  ret\n\npll:\n  mov r8,r15\n  add r8,48\n  mov [z11],r8\n  cmp r15,rbx\n  jne rnd\n  ret\n\npz11:\n  mov rax,1\n  mov rdi,1\n  mov rsi,z11\n  mov rdx,r14\n  syscall\n  ret\n\npbl:\n  mov rax,1\n  mov rdi,1\n  mov rsi,a11\n  mov rdx,1\n  syscall\n  ret",
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
