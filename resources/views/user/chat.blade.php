@extends('layouts.user')
@section('title', 'Chat Admin')

@section('content')
<style>
    .chat-container {
        max-width: 1200px;
        margin: 0 auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        overflow: hidden;
        height: calc(100vh - 200px);
        display: flex;
        flex-direction: column;
    }
    .chat-header {
        background: #dc3545;
        color: white;
        padding: 15px;
        text-align: center;
        font-weight: bold;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
        background: #f8f9fa;
    }
    .message {
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 10px;
        max-width: 70%;
    }
    .message.sent {
        background: #dc3545;
        color: white;
        margin-left: auto;
        text-align: right;
    }
    .message.received {
        background: #e9ecef;
        color: black;
    }
    .message img, .message video {
        max-width: 100%;
        border-radius: 5px;
        cursor: pointer;
    }
    #mediaModal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.8);
        justify-content: center;
        align-items: center;
    }
    .modal-content {
        max-width: 90%;
        max-height: 90%;
        border-radius: 5px;
    }
    .modal-content img, .modal-content video {
        width: 100%;
        height: auto;
    }
    .close {
        position: absolute;
        top: 10px;
        right: 25px;
        color: #fff;
        font-size: 35px;
        font-weight: bold;
        cursor: pointer;
    }
    .chat-input {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        padding: 15px;
        background: white;
        border-top: 1px solid #dee2e6;
    }
    .chat-input input[type="text"] {
        flex: 1;
        border: 1px solid #ced4da;
        border-radius: 20px;
        padding: 10px 15px;
        margin-right: 10px;
    }
    .chat-input input[type="file"] {
        margin-right: 10px;
    }
    .chat-input button {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 20px;
        padding: 10px 20px;
        cursor: pointer;
    }
    .chat-input button:hover {
        background: #c82333;
    }
    .preview {
        margin-top: 10px;
        max-width: 200px;
    }
    .preview img, .preview video {
        max-width: 100%;
        border-radius: 5px;
    }
</style>

<div class="chat-container">
    <div class="chat-header">
        Chat Admin
    </div>
    <div class="chat-messages" id="chat-messages">
        <!-- Messages will be loaded here -->
    </div>
    <div class="chat-input">
        <input type="text" id="message-input" placeholder="Ketik pesan...">
        <input type="file" id="file-input" accept="image/*,video/*" title="Pilih foto atau video (Maks 10MB)">
        <button id="send-button">Kirim</button>
        <div style="flex-basis: 100%; font-size: 0.8rem; color: #6c757d; margin-top: 8px; margin-left: 5px;">
            <svg style="vertical-align: middle; margin-right: 3px;" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
            Mendukung unggahan Foto & Video (Maks. 10 MB)
        </div>
    </div>
    <div id="preview" class="preview" style="display: none;"></div>
</div>

<div id="mediaModal">
    <span class="close" onclick="closeMediaModal()">&times;</span>
    <div class="modal-content" id="modalContent"></div>
</div>

<script>
    const messagesContainer = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const fileInput = document.getElementById('file-input');
    const sendButton = document.getElementById('send-button');
    const preview = document.getElementById('preview');

    function loadMessages() {
        fetch('/user/chat/messages')
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(messages => {
                console.log('Messages:', messages);
                messagesContainer.innerHTML = '';
                messages.forEach(message => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message ' + (message.sender_id === '{{ auth()->id() }}' ? 'sent' : 'received');
                    
                    if (message.message) {
                        const p = document.createElement('p');
                        p.textContent = message.message;
                        p.style.margin = '0 0 5px 0';
                        messageDiv.appendChild(p);
                    }
                    
                    if (message.file_path) {
                        if (message.file_type === 'image') {
                            const img = document.createElement('img');
                            img.src = '/' + message.file_path;
                            img.addEventListener('click', () => openMediaModal(img.src, 'image'));
                            messageDiv.appendChild(img);
                        } else if (message.file_type === 'video') {
                            const videoWrapper = document.createElement('div');
                            videoWrapper.style.position = 'relative';
                            videoWrapper.style.display = 'inline-block';
                            videoWrapper.addEventListener('click', () => openMediaModal('/' + message.file_path, 'video'));
                            const video = document.createElement('video');
                            video.src = '/' + message.file_path;
                            video.controls = true;
                            videoWrapper.appendChild(video);
                            messageDiv.appendChild(videoWrapper);
                        }
                    }
                    
                    // Add timestamp
                    const timeDiv = document.createElement('div');
                    timeDiv.style.fontSize = '0.75rem';
                    timeDiv.style.color = '#999';
                    timeDiv.style.marginTop = '5px';
                    timeDiv.style.textAlign = message.sender_id === '{{ auth()->id() }}' ? 'right' : 'left';
                    const messageDate = new Date(message.created_at);
                    timeDiv.textContent = messageDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    messageDiv.appendChild(timeDiv);
                    
                    messagesContainer.appendChild(messageDiv);
                });
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            })
            .catch(error => {
                console.error('Error loading messages:', error);
            });
    }

    function sendMessage() {
        const message = messageInput.value.trim();
        const file = fileInput.files[0];
        
        if (!message && !file) return;
        
        const formData = new FormData();
        if (message) formData.append('message', message);
        if (file) formData.append('file', file);
        
        fetch('/user/chat/send', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => response.json()).then(data => {
            if (data.success) {
                messageInput.value = '';
                fileInput.value = '';
                preview.style.display = 'none';
                loadMessages();
            } else {
                alert('Gagal mengirim pesan');
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim pesan');
        });
    }

    sendButton.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.innerHTML = '';
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = e.target.result;
                    video.controls = true;
                    preview.appendChild(video);
                }
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
    // Modal functions
    function openMediaModal(src, type) {
        const modal = document.getElementById('mediaModal');
        const modalContent = document.getElementById('modalContent');
        modalContent.innerHTML = '';
        if (type === 'image') {
            const img = document.createElement('img');
            img.src = src;
            modalContent.appendChild(img);
        } else if (type === 'video') {
            const video = document.createElement('video');
            video.src = src;
            video.controls = true;
            video.style.width = '100%';
            modalContent.appendChild(video);
        }
        modal.style.display = 'flex';
    }

    function closeMediaModal() {
        document.getElementById('mediaModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('mediaModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
    loadMessages();
    setInterval(loadMessages, 5000); // Poll every 5 seconds
</script>
@endsection
