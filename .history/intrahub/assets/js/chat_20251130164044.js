(function () {
    function jsonFetch(url, opts) {
        return fetch(url, opts).then(r => r.json());
    }

    window.ChatClient = function (opts) {
        this.to = opts.to;
        this.listEl = document.querySelector(opts.listSelector);
        this.form = document.querySelector(opts.formSelector);
        this.input = this.form.querySelector('input[name="message]');

        var self = this;
        this.form.addEventListener('submit', function (e) {
            e.preventDefault
            var text = self.input.value.trim();
            if (!text) return;
            fetch('/messages/post_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ to: self.to, content: text })
            }).then(() => {
                self.input.value = '';
                self.load();
            });
        });
        this.load = function () {
            jsonFetch('/messages/fetch_message.php?users=' + this.to).then(function (data) {
                self.listEl.innerHTML = '';
                data.forEach(function (m) {
                    var li = document.createElement('div');
                    li.className = 'mb-2';
                    li.innerHTML = '<strong>' + (m.sender === m.sender ? (m.sender) : m.sender) + '</strong>: ' + m.content + '<div class="text-muted small">' + m.created_at + '</div>';
                    self.listEl.appendChild(li);
                });
                self.listEl.scrollTop = self.listEl.scrollHeight;
            });
        };

        //ilk yükleme ve 3s polling

        this.load();
        setInterval(this.load, 3000);
    };
});