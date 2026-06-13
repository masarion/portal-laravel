@extends('layouts.app')
@section('title', isset($project) ? '案件を編集' : '新規案件')
@section('nav_projects', 'active')

@section('extra_style')
.section-card { border:1px solid var(--border); border-radius:var(--radius); background:var(--surface); margin-bottom:1rem; }
.section-header { padding:.85rem 1.1rem; font-weight:600; font-size:.875rem; display:flex; align-items:center; justify-content:space-between; }
.section-body { padding:1rem 1.1rem; border-top:1px solid var(--border); }
.staff-row { display:flex; align-items:center; gap:.5rem; margin-bottom:.5rem; }
@endsection

@section('content')
<div class="page-header d-flex align-items-center gap-3">
  <a href="{{ route('shift.projects.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
  <h2><i class="bi bi-calendar3 me-2 text-primary"></i>{{ isset($project) ? '案件を編集' : '新規案件' }}</h2>
</div>

<form method="POST" id="projectForm"
  action="{{ isset($project) ? route('shift.projects.update', $project) : route('shift.projects.store') }}">
  @csrf
  @isset($project) @method('PUT') @endisset
  <input type="hidden" name="staff_json" id="staffJson">

  <div class="row g-3">
    <div class="col-lg-6">

      {{-- 基本情報 --}}
      <div class="section-card">
        <div class="section-header"><span><i class="bi bi-info-circle me-2 text-primary"></i>基本情報</span></div>
        <div class="section-body">
          <div class="mb-3">
            <label class="form-label fw-semibold small">案件名 <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required
              value="{{ old('name', $project->name ?? '') }}" placeholder="例：2026年7月シフト">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold small">開始日</label>
              <input type="date" name="start_date" class="form-control"
                value="{{ old('start_date', isset($project) ? $project->start_date?->format('Y-m-d') : '') }}">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold small">終了日</label>
              <input type="date" name="end_date" class="form-control"
                value="{{ old('end_date', isset($project) ? $project->end_date?->format('Y-m-d') : '') }}">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">提出期限 <span class="text-danger">*</span></label>
            <input type="datetime-local" name="deadline" class="form-control" required
              value="{{ old('deadline', isset($project) ? $project->deadline->format('Y-m-d\TH:i') : '') }}">
          </div>

          {{-- 認証モード --}}
          <div class="mb-2">
            <label class="form-label fw-semibold small">認証モード</label>
            <div class="d-flex gap-3">
              <label class="d-flex align-items-center gap-2 cursor-pointer">
                <input type="radio" name="auth_mode" value="name" id="authName"
                  {{ old('auth_mode', $project->auth_mode ?? 'name') === 'name' ? 'checked' : '' }}
                  onchange="updateAuthMode()">
                <span class="small">名前入力モード</span>
              </label>
              <label class="d-flex align-items-center gap-2 cursor-pointer">
                <input type="radio" name="auth_mode" value="code" id="authCode"
                  {{ old('auth_mode', $project->auth_mode ?? 'name') === 'code' ? 'checked' : '' }}
                  onchange="updateAuthMode()">
                <span class="small">コード＋パスワードモード</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      {{-- メッセージ設定 --}}
      <div class="section-card">
        <div class="section-header"><span><i class="bi bi-chat-text me-2 text-primary"></i>メッセージ設定</span></div>
        <div class="section-body">
          <div class="mb-3">
            <label class="form-label fw-semibold small">案内文</label>
            <textarea name="info_message" class="form-control form-control-sm" rows="2"
              placeholder="スタッフへの案内文">{{ old('info_message', $project->info_message ?? '') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">コピーガイド</label>
            <textarea name="copy_guide_message" class="form-control form-control-sm" rows="2"
              placeholder="入力方法の説明">{{ old('copy_guide_message', $project->copy_guide_message ?? '') }}</textarea>
          </div>
          <div class="mb-0">
            <label class="form-label fw-semibold small">確認メッセージ</label>
            <textarea name="confirm_message" class="form-control form-control-sm" rows="2"
              placeholder="提出前の確認メッセージ">{{ old('confirm_message', $project->confirm_message ?? '') }}</textarea>
          </div>
        </div>
      </div>

    </div>

    <div class="col-lg-6">

      {{-- スタッフ登録 --}}
      <div class="section-card">
        <div class="section-header">
          <span><i class="bi bi-people me-2 text-primary"></i>スタッフ登録 <span id="staffCount" class="badge bg-primary bg-opacity-10 text-primary ms-1">0</span></span>
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="addStaffRow()"><i class="bi bi-plus"></i> 追加</button>
        </div>
        <div class="section-body">
          <div class="d-flex gap-1 mb-2 small text-muted" id="staffHeader">
            <div style="flex:1">氏名 *</div>
            <div id="codeHeader" style="flex:0 0 90px">コード</div>
            <div id="pwHeader" style="flex:0 0 90px;display:none">パスワード</div>
            <div style="flex:0 0 32px"></div>
          </div>
          <div id="staffList"></div>
          <button type="button" class="btn btn-sm btn-outline-secondary w-100 mt-1" onclick="toggleCsvPanel()">
            <i class="bi bi-upload me-1"></i>CSV/Excel一括貼り付け
          </button>
          <div id="csvPanel" style="display:none;" class="mt-2">
            <textarea id="csvText" class="form-control form-control-sm mb-2" rows="4"
              placeholder="コード&#9;氏名 の形式で貼り付け（コード省略可）"></textarea>
            <button type="button" class="btn btn-sm btn-primary" onclick="importCsv()">インポート</button>
          </div>
        </div>
      </div>

      {{-- 提出リンク（編集時のみ） --}}
      @isset($project)
      @if(isset($submitUrl))
      <div class="card mb-3">
        <div class="card-body py-3">
          <div class="fw-semibold small mb-2"><i class="bi bi-link-45deg me-1 text-primary"></i>スタッフへの提出リンク（全員共通）</div>
          <div class="d-flex align-items-center gap-2 mb-3">
            <code class="small bg-light px-2 py-1 rounded flex-grow-1" style="word-break:break-all;">{{ $submitUrl }}</code>
            <button type="button" class="btn btn-sm btn-outline-secondary"
              onclick="navigator.clipboard.writeText('{{ $submitUrl }}');this.innerHTML='<i class=\'bi bi-check\'></i>';">
              <i class="bi bi-copy"></i>
            </button>
          </div>
          {{-- 印刷ボタン --}}
          <div class="d-flex align-items-end gap-3">
            <img id="qrCodeImg" src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($submitUrl) }}"
                 alt="QRコード" style="border-radius:8px;border:1px solid #e2e8f0;">
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#printModal">
              <i class="bi bi-printer me-1"></i>印刷
            </button>
          </div>
        </div>
      </div>
      @endif
      @endisset

    </div>
  </div>

  <iframe id="printFrame" style="display:none;"></iframe>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>保存する</button>
    <a href="{{ route('shift.projects.index') }}" class="btn btn-outline-secondary">キャンセル</a>
  </div>
</form>

@isset($project)
@if(isset($submitUrl))
{{-- 印刷モーダル --}}
<div class="modal fade" id="printModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title fw-semibold"><i class="bi bi-printer me-2 text-primary"></i>印刷プレビュー</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <div class="d-flex" style="min-height:600px;">
          {{-- 左：編集パネル --}}
          <div style="width:320px;flex-shrink:0;border-right:1px solid #e2e8f0;overflow-y:auto;padding:1rem;">
            <div class="mb-4">
              <div class="fw-semibold small text-primary mb-2"><i class="bi bi-type me-1"></i>表題</div>
              <input type="text" id="pt-title" class="form-control form-control-sm mb-2" value="{{ $project->name }}">
              <div class="d-flex gap-2 mb-2">
                <select id="pt-title-size" class="form-select form-select-sm">
                  <option value="14">小 (14px)</option><option value="20" selected>中 (20px)</option>
                  <option value="26">大 (26px)</option><option value="32">特大 (32px)</option>
                </select>
                <select id="pt-title-weight" class="form-select form-select-sm">
                  <option value="400">標準</option><option value="700" selected>太字</option>
                </select>
              </div>
              <div id="pt-title-color-wrap"></div>
            </div>
            <div class="mb-4">
              <div class="fw-semibold small text-primary mb-2"><i class="bi bi-text-left me-1"></i>自由入力（任意）</div>
              <textarea id="pt-body" class="form-control form-control-sm mb-2" rows="3" placeholder="例：〇〇月のシフト希望をご提出ください"></textarea>
              <div class="d-flex gap-2 mb-2">
                <select id="pt-body-size" class="form-select form-select-sm">
                  <option value="10">小</option><option value="13" selected>中</option><option value="16">大</option><option value="20">特大</option>
                </select>
                <select id="pt-body-weight" class="form-select form-select-sm">
                  <option value="400" selected>標準</option><option value="700">太字</option>
                </select>
              </div>
              <div id="pt-body-color-wrap"></div>
            </div>
            <div class="mb-4">
              <div class="fw-semibold small text-primary mb-2"><i class="bi bi-qr-code me-1"></i>QRコードサイズ</div>
              <div class="d-flex align-items-center gap-2">
                <input type="range" id="pt-qr-size" class="form-range flex-grow-1" min="80" max="280" step="10" value="160">
                <span id="pt-qr-size-label" class="small text-muted" style="width:50px;text-align:right;">160px</span>
              </div>
            </div>
            <div class="mb-4">
              <div class="fw-semibold small text-primary mb-2"><i class="bi bi-image me-1"></i>QRコード中央に挿入（任意）</div>
              <div class="d-flex gap-3 mb-2 small">
                <label class="d-flex align-items-center gap-1"><input type="radio" name="pt-overlay-type" value="none" checked onchange="onOverlayTypeChange()"> なし</label>
                <label class="d-flex align-items-center gap-1"><input type="radio" name="pt-overlay-type" value="text" onchange="onOverlayTypeChange()"> テキスト</label>
                <label class="d-flex align-items-center gap-1"><input type="radio" name="pt-overlay-type" value="image" onchange="onOverlayTypeChange()"> 画像</label>
              </div>
              <div id="pt-overlay-text-wrap" style="display:none;">
                <input type="text" id="pt-overlay-text" class="form-control form-control-sm mb-2" maxlength="6" placeholder="例：会社名">
                <select id="pt-overlay-text-size" class="form-select form-select-sm mb-2">
                  <option value="small">小</option><option value="medium" selected>中</option><option value="large">大</option>
                </select>
                <div id="pt-overlay-text-color-wrap"></div>
              </div>
              <div id="pt-overlay-image-wrap" style="display:none;">
                <label class="btn btn-sm btn-outline-secondary w-100">
                  <i class="bi bi-upload me-1"></i>画像を選択（PNG / JPG / SVG）
                  <input type="file" id="pt-overlay-image" accept="image/png,image/jpeg,image/svg+xml" style="display:none;" onchange="onOverlayImageChange(this)">
                </label>
                <div id="pt-overlay-image-preview" class="mt-2 text-center" style="display:none;">
                  <img id="pt-overlay-image-thumb" src="" alt="" style="max-height:48px;border-radius:4px;border:1px solid #e2e8f0;">
                </div>
              </div>
              <p class="text-muted mt-2 mb-0" style="font-size:10px;">※ 面積の約20%以内に白丸で挿入されます。</p>
            </div>
            <div class="mb-2">
              <div class="fw-semibold small text-primary mb-2"><i class="bi bi-exclamation-circle me-1"></i>注意事項（任意）</div>
              <textarea id="pt-note" class="form-control form-control-sm mb-2" rows="3" placeholder="例：提出期限は〇月〇日です"></textarea>
              <div class="d-flex gap-2 mb-2">
                <select id="pt-note-size" class="form-select form-select-sm">
                  <option value="9">小</option><option value="12" selected>中</option><option value="15">大</option><option value="18">特大</option>
                </select>
                <select id="pt-note-weight" class="form-select form-select-sm">
                  <option value="400" selected>標準</option><option value="700">太字</option>
                </select>
              </div>
              <div id="pt-note-color-wrap"></div>
            </div>
          </div>
          {{-- 右：A4プレビュー --}}
          <div class="flex-grow-1 d-flex align-items-start justify-content-center" style="background:#f0f2f5;padding:2rem;overflow:auto;">
            <div id="printPreview" style="width:210mm;min-height:297mm;background:#fff;box-shadow:0 4px 24px rgba(0,0,0,.15);border-radius:4px;padding:20mm 15mm;display:flex;flex-direction:column;align-items:center;justify-content:center;font-family:'Noto Sans JP',sans-serif;box-sizing:border-box;">
              <div id="prev-title" style="text-align:center;margin-bottom:12px;"></div>
              <div id="prev-body" style="text-align:center;margin-bottom:20px;white-space:pre-wrap;"></div>
              <canvas id="prev-qr" style="border-radius:8px;border:1px solid #e2e8f0;margin-bottom:20px;"></canvas>
              <div id="prev-note" style="text-align:center;white-space:pre-wrap;"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">閉じる</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="executePrint()"><i class="bi bi-printer me-1"></i>印刷する</button>
      </div>
    </div>
  </div>
</div>
@endif
@endisset

@endsection

@section('scripts')
<script>
const PROJECT_AUTH_MODE = '{{ old('auth_mode', $project->auth_mode ?? 'name') }}';
let staffList = @isset($project) {!! json_encode($project->staffMembers->map(fn($s) => ['id'=>$s->id,'name'=>$s->name,'code'=>$s->code,'password'=>$s->password])) !!} @else [] @endisset;

function updateAuthMode() {
  const mode = document.querySelector('input[name="auth_mode"]:checked')?.value;
  const showPw = mode === 'code';
  document.getElementById('pwHeader').style.display = showPw ? '' : 'none';
  document.getElementById('codeHeader').style.display = '';
  renderStaffList();
}

function renderStaffList(filter='') {
  const list = document.getElementById('staffList');
  const mode = document.querySelector('input[name="auth_mode"]:checked')?.value || 'name';
  list.innerHTML = '';
  staffList.forEach((s, i) => {
    if (filter && !s.name.includes(filter)) return;
    const row = document.createElement('div');
    row.className = 'staff-row';
    row.innerHTML = `
      <input type="text" class="form-control form-control-sm" placeholder="氏名 *" value="${s.name}"
        oninput="staffList[${i}].name=this.value;updateStaffCount()" style="flex:1">
      <input type="text" class="form-control form-control-sm" placeholder="コード" value="${s.code}"
        oninput="staffList[${i}].code=this.value" style="flex:0 0 90px">
      <input type="text" class="form-control form-control-sm" placeholder="PW" value="${s.password}"
        oninput="staffList[${i}].password=this.value" style="flex:0 0 90px;display:${mode==='code'?'':'none'}">
      <button type="button" class="btn btn-sm btn-outline-danger px-2" onclick="removeStaff(${i})"><i class="bi bi-x"></i></button>`;
    list.appendChild(row);
  });
  updateStaffCount();
}

function addStaffRow() {
  staffList.push({id:null,name:'',code:'',password:''});
  renderStaffList();
  document.querySelectorAll('#staffList input[placeholder="氏名 *"]').forEach((el,i,a)=>{ if(i===a.length-1) el.focus(); });
}

function removeStaff(i) { staffList.splice(i,1); renderStaffList(); }
function updateStaffCount() {
  document.getElementById('staffCount').textContent = staffList.filter(s=>s.name.trim()).length;
}

function toggleCsvPanel() {
  const p = document.getElementById('csvPanel');
  p.style.display = p.style.display === 'none' ? '' : 'none';
}

function importCsv() {
  const text = document.getElementById('csvText').value.trim();
  if (!text) return;
  let added = 0;
  text.split('\n').forEach(line => {
    line = line.trim(); if (!line) return;
    const delim = line.includes('\t') ? '\t' : ',';
    const parts = line.split(delim);
    const code = parts.length > 1 ? parts[0].trim() : '';
    const name = parts.length > 1 ? parts[1].trim() : parts[0].trim();
    if (!name) return;
    staffList.push({id:null,name,code,password:''}); added++;
  });
  renderStaffList();
  document.getElementById('csvText').value = '';
  document.getElementById('csvPanel').style.display = 'none';
  alert(`${added}名をインポートしました`);
}

document.getElementById('projectForm').addEventListener('submit', function() {
  document.getElementById('staffJson').value = JSON.stringify(staffList);
});

updateAuthMode();

// ── 印刷プレビュー ──
@isset($project)
@if(isset($submitUrl))
const QR_DATA = '{{ urlencode($submitUrl) }}';
const QR_API  = `https://api.qrserver.com/v1/create-qr-code/?data=${QR_DATA}&size=`;
const BASIC_COLORS = ['#000000','#333333','#555555','#888888','#aaaaaa','#cccccc','#ffffff','#c0392b','#e74c3c','#e67e22','#f39c12','#f1c40f','#2ecc71','#27ae60','#1abc9c','#16a085','#3498db','#2980b9','#2c3e50','#2e86c1','#1a5276','#8e44ad','#6c3483','#d35400','#a04000','#117a65','#0e6655','#1f618d','#7d6608','#6e2f1a'];
const textColors = {title:'#000000',body:'#333333',note:'#555555',overlay:'#000000'};
let overlayImageDataUrl = null;

function buildColorPicker(wrapperId, colorKey, onChange) {
  const wrap = document.getElementById(wrapperId);
  if (!wrap) return;
  const grid = document.createElement('div');
  grid.style.cssText = 'display:flex;flex-wrap:wrap;gap:4px;margin-bottom:6px;';
  BASIC_COLORS.forEach(c => {
    const sw = document.createElement('div');
    sw.style.cssText = `width:20px;height:20px;border-radius:4px;background:${c};cursor:pointer;border:2px solid ${c===textColors[colorKey]?'#2563eb':'transparent'};box-sizing:border-box;flex-shrink:0;`;
    sw.onclick = () => { grid.querySelectorAll('div').forEach(s=>s.style.borderColor='transparent'); sw.style.borderColor='#2563eb'; customInput.value=c; textColors[colorKey]=c; onChange(c); };
    grid.appendChild(sw);
  });
  const customRow = document.createElement('div');
  customRow.style.cssText = 'display:flex;align-items:center;gap:6px;';
  const lbl = document.createElement('span'); lbl.textContent='その他'; lbl.style.cssText='font-size:11px;color:#555;white-space:nowrap;';
  const customInput = document.createElement('input'); customInput.type='color'; customInput.value=textColors[colorKey];
  customInput.style.cssText='width:32px;height:28px;padding:0;border:1px solid #ccc;border-radius:4px;cursor:pointer;';
  customInput.oninput = () => { grid.querySelectorAll('div').forEach(s=>s.style.borderColor='transparent'); textColors[colorKey]=customInput.value; onChange(customInput.value); };
  customRow.appendChild(lbl); customRow.appendChild(customInput);
  wrap.appendChild(grid); wrap.appendChild(customRow);
}

function drawQRCanvas(canvas, px, overlayType, overlayText, overlayTextSize, imgDataUrl, onDone) {
  canvas.width = px; canvas.height = px;
  const ctx = canvas.getContext('2d');
  const qrImg = new Image(); qrImg.crossOrigin='anonymous';
  qrImg.src = QR_API + '400x400';
  qrImg.onload = () => {
    ctx.drawImage(qrImg, 0, 0, px, px);
    if (overlayType === 'none') { if(onDone) onDone(); return; }
    const r = px * 0.21;
    ctx.save(); ctx.beginPath(); ctx.arc(px/2,px/2,r,0,Math.PI*2); ctx.fillStyle='#fff'; ctx.fill(); ctx.restore();
    if (overlayType === 'text' && overlayText) {
      const fsMap = {small:Math.floor(r*.7),medium:Math.floor(r*1),large:Math.floor(r*1.3)};
      const fs = fsMap[overlayTextSize]||fsMap.medium;
      ctx.save(); ctx.font=`bold ${fs}px sans-serif`; ctx.fillStyle=textColors.overlay; ctx.textAlign='center'; ctx.textBaseline='middle';
      const maxW = r*1.6; let line='',lines=[];
      overlayText.split('').forEach(ch=>{ const t=line+ch; if(ctx.measureText(t).width>maxW&&line){lines.push(line);line=ch;}else line=t; });
      if(line) lines.push(line); lines=lines.slice(0,2);
      const lh=fs*1.2,sy=px/2-(lines.length-1)*lh/2;
      lines.forEach((l,i)=>ctx.fillText(l,px/2,sy+i*lh)); ctx.restore(); if(onDone) onDone();
    } else if (overlayType==='image' && imgDataUrl) {
      const oImg=new Image(); oImg.onload=()=>{ const mw=r*1.56,s=Math.min(mw/oImg.width,mw/oImg.height),dw=oImg.width*s,dh=oImg.height*s; ctx.drawImage(oImg,px/2-dw/2,px/2-dh/2,dw,dh); if(onDone) onDone(); }; oImg.src=imgDataUrl;
    } else { if(onDone) onDone(); }
  };
}

function getOverlayState() {
  return { type: document.querySelector('input[name="pt-overlay-type"]:checked')?.value||'none', text: document.getElementById('pt-overlay-text')?.value||'', textSize: document.getElementById('pt-overlay-text-size')?.value||'medium' };
}

function updatePreview() {
  const titleEl=document.getElementById('prev-title'); titleEl.textContent=document.getElementById('pt-title').value; titleEl.style.fontSize=document.getElementById('pt-title-size').value+'px'; titleEl.style.fontWeight=document.getElementById('pt-title-weight').value; titleEl.style.color=textColors.title;
  const bodyEl=document.getElementById('prev-body'); bodyEl.textContent=document.getElementById('pt-body').value; bodyEl.style.fontSize=document.getElementById('pt-body-size').value+'px'; bodyEl.style.fontWeight=document.getElementById('pt-body-weight').value; bodyEl.style.color=textColors.body;
  const qrSize=parseInt(document.getElementById('pt-qr-size').value); document.getElementById('pt-qr-size-label').textContent=qrSize+'px';
  const {type,text,textSize}=getOverlayState();
  drawQRCanvas(document.getElementById('prev-qr'),qrSize,type,text,textSize,overlayImageDataUrl);
  const noteEl=document.getElementById('prev-note'); noteEl.textContent=document.getElementById('pt-note').value; noteEl.style.fontSize=document.getElementById('pt-note-size').value+'px'; noteEl.style.fontWeight=document.getElementById('pt-note-weight').value; noteEl.style.color=textColors.note;
}

function onOverlayTypeChange() {
  const type=document.querySelector('input[name="pt-overlay-type"]:checked')?.value;
  document.getElementById('pt-overlay-text-wrap').style.display=type==='text'?'':'none';
  document.getElementById('pt-overlay-image-wrap').style.display=type==='image'?'':'none';
  updatePreview();
}

function onOverlayImageChange(input) {
  const file=input.files[0]; if(!file) return;
  const reader=new FileReader(); reader.onload=e=>{ overlayImageDataUrl=e.target.result; document.getElementById('pt-overlay-image-thumb').src=overlayImageDataUrl; document.getElementById('pt-overlay-image-preview').style.display=''; updatePreview(); }; reader.readAsDataURL(file);
}

function executePrint() {
  const titleText=document.getElementById('pt-title').value,bodyText=document.getElementById('pt-body').value,noteText=document.getElementById('pt-note').value;
  const qrSize=parseInt(document.getElementById('pt-qr-size').value);
  const titleEl=document.getElementById('prev-title'),bodyEl=document.getElementById('prev-body'),noteEl=document.getElementById('prev-note');
  const ts=`font-size:${titleEl.style.fontSize};font-weight:${titleEl.style.fontWeight};color:${textColors.title};`;
  const bs=`font-size:${bodyEl.style.fontSize};font-weight:${bodyEl.style.fontWeight};color:${textColors.body};`;
  const ns=`font-size:${noteEl.style.fontSize};font-weight:${noteEl.style.fontWeight};color:${textColors.note};`;
  const esc=s=>s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  const {type,text,textSize}=getOverlayState();
  const pc=document.createElement('canvas'),pp=Math.max(qrSize*2,400);
  drawQRCanvas(pc,pp,type,text,textSize,overlayImageDataUrl,()=>{
    const qrUrl=pc.toDataURL('image/png');
    const html=`<!DOCTYPE html><html><head><meta charset="UTF-8"><style>@import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap');*{box-sizing:border-box;margin:0;padding:0;}body{font-family:'Noto Sans JP',sans-serif;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;padding:20mm 15mm;}.title{text-align:center;margin-bottom:12px;${ts}}.body-text{text-align:center;margin-bottom:20px;white-space:pre-wrap;${bs}}.qr{margin-bottom:20px;}.qr img{display:block;border-radius:8px;border:1px solid #e2e8f0;width:${qrSize}px;height:${qrSize}px;}.note{text-align:center;white-space:pre-wrap;${ns}}@media print{@page{size:A4;margin:10mm;}}</style></head><body>${titleText?`<div class="title">${esc(titleText)}</div>`:''} ${bodyText?`<div class="body-text">${esc(bodyText)}</div>`:''}<div class="qr"><img src="${qrUrl}" alt="QR"></div>${noteText?`<div class="note">${esc(noteText)}</div>`:''}</body></html>`;
    const frame=document.getElementById('printFrame'); frame.onload=()=>{frame.contentWindow.focus();frame.contentWindow.print();}; frame.srcdoc=html;
  });
}

document.addEventListener('DOMContentLoaded', () => {
  buildColorPicker('pt-title-color-wrap','title',c=>{textColors.title=c;updatePreview();});
  buildColorPicker('pt-body-color-wrap','body',c=>{textColors.body=c;updatePreview();});
  buildColorPicker('pt-note-color-wrap','note',c=>{textColors.note=c;updatePreview();});
  buildColorPicker('pt-overlay-text-color-wrap','overlay',c=>{textColors.overlay=c;updatePreview();});
  updatePreview();
  ['pt-title','pt-title-size','pt-title-weight','pt-body','pt-body-size','pt-body-weight','pt-qr-size','pt-note','pt-note-size','pt-note-weight','pt-overlay-text','pt-overlay-text-size'].forEach(id=>{
    const el=document.getElementById(id); if(el) el.addEventListener('input',updatePreview);
  });
});
@endif
@endisset
</script>
@endsection
