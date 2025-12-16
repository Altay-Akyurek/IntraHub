// assets/js/pulses.js - small helper to add/remove question rows in admin form
(function () {
    function qRowHtml(idx) {
        return `
      <div class="q-row card p-2 mb-2" data-qi="${idx}">
        <div class="d-flex justify-content-between">
          <strong>Soru ${idx + 1}</strong>
          <button type="button" class="btn btn-sm btn-danger remove-q">Kaldır</button>
        </div>
        <div class="mt-2">
          <input type="text" name="q_text_${idx}" class="form-control q-text" placeholder="Soru metni" required>
        </div>
        <div class="mt-2">
          <select name="q_type_${idx}" class="form-select q-type">
            <option value="scale">1-5 Ölçek</option>
            <option value="text">Serbest Metin</option>
          </select>
        </div>
      </div>
    `;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var qList = document.getElementById('questionsList');
        var addBtn = document.getElementById('addQuestionBtn');
        var form = document.getElementById('pulseCreateForm');

        var nextIdx = 0;
        function addQuestion(prefilled) {
            qList.insertAdjacentHTML('beforeend', qRowHtml(nextIdx));
            if (prefilled) {
                var row = qList.querySelector('[data-qi="' + nextIdx + '"]');
                row.querySelector('.q-text').value = prefilled.text || '';
                row.querySelector('.q-type').value = prefilled.type || 'scale';
            }
            nextIdx++;
        }

        addBtn.addEventListener('click', function () { addQuestion(); });

        qList.addEventListener('click', function (e) {
            if (e.target.matches('.remove-q')) {
                var row = e.target.closest('.q-row');
                row.remove();
            }
        });

        // On submit, gather questions into hidden JSON field and post via fetch
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var rows = qList.querySelectorAll('.q-row');
            if (rows.length === 0) { alert('En az bir soru ekleyin.'); return; }
            var questions = [];
            rows.forEach(function (r) {
                var t = r.querySelector('.q-text').value.trim();
                var ty = r.querySelector('.q-type').value;
                if (t === '') return;
                questions.push({ text: t, type: ty });
            });

            var fd = new FormData(form);
            // build payload
            var payload = {
                action: 'create',
                title: fd.get('title'),
                description: fd.get('description'),
                send_at: fd.get('send_at') || null,
                active: fd.get('active') ? 1 : 0,
                questions: questions
            };

            fetch('/pulses/api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            }).then(r => r.json()).then(function (resp) {
                if (resp.ok) {
                    location.reload();
                } else {
                    alert('Hata: ' + (resp.error || 'Bilinmeyen'));
                }
            }).catch(function (err) { alert('İstek başarısız: ' + err); });
        });

        // add one default question initially
        addQuestion();
    });
})();