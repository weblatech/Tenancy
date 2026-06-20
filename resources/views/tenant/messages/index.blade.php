<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtoupper($tenantId) }} - Customer Messages</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.05) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(14, 165, 233, 0.05) 0px, transparent 50%);
            background-size: cover;
            background-attachment: fixed;
        }
        .dotted-overlay {
            background-image: radial-gradient(#cbd5e1 0.8px, transparent 0.8px);
            background-size: 24px 24px;
        }
        .card-premium {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased bg-slate-50/50 pb-16 relative overflow-x-hidden">
    
    <div class="absolute inset-0 dotted-overlay opacity-30 pointer-events-none z-0"></div>
    
    <!-- Top Premium Navigation Bar -->
    <nav class="bg-slate-950 border-b border-slate-800 sticky top-0 z-50 shadow-md relative z-15">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <!-- Left Brand Info -->
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 text-white p-2.5 rounded-xl shadow-lg shadow-indigo-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-indigo-400 text-[10px] font-bold block uppercase tracking-wider">Messages Inbox</span>
                    </div>
                </div>
                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <a href="/shop" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <span class="text-slate-700">|</span>
                    <a href="http://{{ $tenantId }}.localhost:8000" target="_blank" class="text-slate-400 hover:text-white text-xs font-bold transition">View Storefront ↗</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="relative z-10 max-w-7xl mx-auto mt-10 px-6">
        
        <!-- Success/Error Alert -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl font-bold flex items-center gap-3 shadow-sm">
                <span class="text-lg">✅</span>
                <span class="text-xs font-semibold">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-2xl font-bold flex flex-col gap-2 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-lg">⚠️</span>
                    <span class="text-xs font-semibold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Customer Inbox</h2>
                <p class="text-slate-500 font-medium text-xs mt-1">Review contact inquiries, view submitter IPs, and reply instantly.</p>
            </div>
            <div class="text-xs bg-slate-900 text-slate-300 font-black px-4 py-2.5 rounded-xl border border-slate-800 flex items-center gap-2">
                <span>✉️</span>
                <span>{{ count($contactMessages) }} Total Messages</span>
            </div>
        </div>

        <!-- Messages Table -->
        <div class="card-premium relative overflow-hidden rounded-3xl shadow-sm">
            <div class="absolute top-0 left-0 w-32 h-[4px] bg-blue-500"></div>
            @if(count($contactMessages) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black uppercase text-slate-500 tracking-wider">
                                <th class="p-4 pl-6">Date</th>
                                <th class="p-4">Sender Info</th>
                                <th class="p-4">Subject</th>
                                <th class="p-4">Message</th>
                                <th class="p-4">Client IP</th>
                                <th class="p-4 pr-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-150 text-xs font-semibold text-slate-700">
                            @foreach($contactMessages as $msg)
                                <tr class="hover:bg-slate-50/40 transition">
                                    <td class="p-4 pl-6 text-[10px] text-slate-400 font-mono">
                                        {{ date('M d, Y h:i A', strtotime($msg['created_at'])) }}
                                    </td>
                                    <td class="p-4 leading-tight">
                                        <div class="font-extrabold text-slate-800">{{ $msg['name'] }}</div>
                                        <div class="text-[10px] text-indigo-600 mt-0.5">{{ $msg['email'] }}</div>
                                    </td>
                                    <td class="p-4 font-extrabold text-slate-800 max-w-[150px] truncate" title="{{ $msg['subject'] ?? 'No Subject' }}">
                                        {{ $msg['subject'] ?? 'No Subject' }}
                                    </td>
                                    <td class="p-4 text-slate-500 max-w-[300px] truncate leading-relaxed" title="{{ $msg['message'] }}">
                                        {{ $msg['message'] }}
                                    </td>
                                    <td class="p-4">
                                        <span class="bg-slate-100 text-slate-600 text-[10px] px-2.5 py-1 rounded-lg font-mono font-bold">
                                            {{ $msg['ip'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="p-4 pr-6 text-right space-x-1.5 whitespace-nowrap">
                                        <button type="button" 
                                            onclick="openReplyModal('{{ $msg['email'] }}', '{{ addslashes($msg['subject'] ?? 'No Subject') }}', '{{ addslashes($msg['message']) }}')" 
                                            class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px] font-black px-3 py-2 rounded-lg transition inline-flex items-center gap-1 shadow-sm">
                                            <span>💬</span> Reply
                                        </button>
                                        <a href="/shop/messages/delete/{{ $msg['original_index'] }}" 
                                            onclick="return confirm('Are you sure you want to delete this message?');" 
                                            class="bg-rose-50 hover:bg-rose-100 text-rose-700 text-[10px] font-black px-3 py-2 rounded-lg transition inline-flex items-center gap-1 shadow-sm">
                                            <span>🗑️</span> Delete
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-20 bg-slate-50/20">
                    <span class="text-5xl mb-4 block">✉️</span>
                    <h3 class="text-sm font-extrabold text-slate-850">No customer messages</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto leading-relaxed">Your customers haven't submitted any inquiries via the contact form yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Reply Modal Overlay -->
    <div id="reply-modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full border border-slate-200 overflow-hidden shadow-2xl transition duration-200 transform scale-95 opacity-0" id="reply-modal-content">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-950 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2 text-white">
                    <span class="text-lg">📧</span>
                    <h3 class="text-xs uppercase tracking-wider font-extrabold">Compose Email Reply</h3>
                </div>
                <button type="button" onclick="closeReplyModal()" class="text-slate-400 hover:text-white transition font-black text-sm">✕</button>
            </div>

            <!-- Modal Form -->
            <form action="/shop/messages/reply" method="POST" class="p-6 space-y-4">
                @csrf
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">To</label>
                    <input type="email" name="email" id="reply-email" readonly class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Subject</label>
                    <input type="text" name="subject" id="reply-subject" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:border-indigo-500 focus:bg-white transition">
                </div>

                <!-- Original Message Preview -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">Original Inquiry</label>
                    <div id="reply-original-body" class="text-xs text-slate-500 font-medium whitespace-pre-wrap leading-relaxed max-h-32 overflow-y-auto italic"></div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Message Reply Body</label>
                    <textarea name="message" id="reply-message" rows="5" required placeholder="Type your reply to the customer here..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:border-indigo-500 focus:bg-white transition leading-relaxed"></textarea>
                </div>

                <!-- Footer Actions -->
                <div class="pt-4 border-t border-slate-150 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <!-- Manual Email Fallback button (Always populated with client mailto details) -->
                    <a id="reply-mailto-btn" href="#" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold px-5 py-3 rounded-xl transition text-center shadow-sm flex items-center justify-center gap-1.5">
                        📬 Open Mail Client (Manual Fallback)
                    </a>
                    
                    <div class="flex gap-2 shrink-0">
                        <button type="button" onclick="closeReplyModal()" class="w-full sm:w-auto bg-slate-50 hover:bg-slate-150 text-slate-600 text-xs font-extrabold px-5 py-3 rounded-xl transition shadow-sm border border-slate-250">
                            Cancel
                        </button>
                        <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold px-6 py-3 rounded-xl transition shadow-md shadow-indigo-600/10">
                            Send Reply
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Script to manage modal -->
    <script>
        const modal = document.getElementById('reply-modal');
        const modalContent = document.getElementById('reply-modal-content');
        
        const emailInput = document.getElementById('reply-email');
        const subjectInput = document.getElementById('reply-subject');
        const originalBodyDiv = document.getElementById('reply-original-body');
        const messageTextarea = document.getElementById('reply-message');
        const mailtoBtn = document.getElementById('reply-mailto-btn');

        function updateMailtoLink() {
            const email = encodeURIComponent(emailInput.value);
            const subject = encodeURIComponent(subjectInput.value);
            const body = encodeURIComponent(messageTextarea.value);
            mailtoBtn.href = `mailto:${email}?subject=${subject}&body=${body}`;
        }

        // Hook listeners to dynamic update
        subjectInput.addEventListener('input', updateMailtoLink);
        messageTextarea.addEventListener('input', updateMailtoLink);

        function openReplyModal(email, subject, originalBody) {
            emailInput.value = email;
            subjectInput.value = `Re: ${subject}`;
            originalBodyDiv.textContent = originalBody;
            messageTextarea.value = ''; // Reset body
            
            updateMailtoLink();

            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeReplyModal() {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        // Close on overlay click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeReplyModal();
            }
        });
    </script>
</body>
</html>
