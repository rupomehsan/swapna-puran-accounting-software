<template>
  <div>
    <div v-if="visible" class="inv-backdrop" @click.self="close">
      <div class="inv-modal">

        <div class="inv-modal__header">
          <h6 class="inv-modal__title"><i class="fa fa-file-text-o mr-2"></i>Deposit Receipt</h6>
          <div class="inv-modal__header-btns">
            <button class="btn btn-sm btn-outline-primary mr-2" @click="printInvoice">
              <i class="fa fa-print mr-1"></i> Print
            </button>
            <button class="btn btn-sm btn-outline-secondary" @click="close">
              <i class="fa fa-times"></i>
            </button>
          </div>
        </div>

        <div class="inv-body" id="deposit-invoice-print">

          <!-- Header -->
          <div class="inv-top">
            <div class="inv-top__left">
              <div v-if="orgLogo" class="inv-logo-wrap">
                <img :src="orgLogo" class="inv-logo" alt="logo" />
              </div>
              <div v-else class="inv-logo-initials">{{ orgInitials }}</div>
              <div class="inv-org-info">
                <div class="inv-org-name">{{ orgName }}</div>
                <div class="inv-org-sub">Member Deposit Receipt</div>
              </div>
            </div>
            <div class="inv-top__right">
              <div class="inv-voucher">{{ item.voucher_no || '—' }}</div>
              <div class="inv-date">Date: <b>{{ fmtDate(item.payment_date || item.created_at) }}</b></div>
            </div>
          </div>

          <div class="inv-rule"></div>

          <!-- Two-column info -->
          <div class="inv-cols">
            <div class="inv-col-block">
              <div class="inv-col-title">Member Information</div>
              <div class="inv-row-item">
                <span class="inv-row-lbl">Member Name</span>
                <span class="inv-row-sep">:</span>
                <span class="inv-row-val">{{ item.member?.name || '—' }}</span>
              </div>
              <div class="inv-row-item">
                <span class="inv-row-lbl">Phone</span>
                <span class="inv-row-sep">:</span>
                <span class="inv-row-val">{{ item.member?.phone || '—' }}</span>
              </div>
              <div class="inv-row-item">
                <span class="inv-row-lbl">Email</span>
                <span class="inv-row-sep">:</span>
                <span class="inv-row-val">{{ item.member?.email || '—' }}</span>
              </div>
              <div class="inv-row-item">
                <span class="inv-row-lbl">Total Shares</span>
                <span class="inv-row-sep">:</span>
                <span class="inv-row-val">{{ item.member?.number_of_share || '—' }} shares</span>
              </div>
            </div>
            <div class="inv-col-block">
              <div class="inv-col-title">Deposit Details</div>
              <div class="inv-row-item">
                <span class="inv-row-lbl">Deposit Type</span>
                <span class="inv-row-sep">:</span>
                <span class="inv-row-val">
                  <span class="inv-chip" :class="item.deposit_type === 'share_deposit' ? 'inv-chip--purple' : 'inv-chip--blue'">
                    {{ item.deposit_type === 'share_deposit' ? 'Share Deposit' : 'Extra Savings' }}
                  </span>
                </span>
              </div>
              <div class="inv-row-item">
                <span class="inv-row-lbl">For Month</span>
                <span class="inv-row-sep">:</span>
                <span class="inv-row-val">{{ fmtMonth(item.for_month) }}</span>
              </div>
              <div class="inv-row-item">
                <span class="inv-row-lbl">Payment Method</span>
                <span class="inv-row-sep">:</span>
                <span class="inv-row-val">{{ item.payment_method || '—' }}</span>
              </div>
              <div class="inv-row-item">
                <span class="inv-row-lbl">Payment Date</span>
                <span class="inv-row-sep">:</span>
                <span class="inv-row-val">{{ fmtDate(item.payment_date) }}</span>
              </div>
            </div>
          </div>

          <!-- Amount -->
          <div class="inv-amount-wrap">
            <div class="inv-amount-box">
              <div class="inv-amount-lbl">Total Amount Received</div>
              <div class="inv-amount-val">৳ {{ fmt(item.amount) }}</div>
            </div>
          </div>

          <!-- Note -->
          <div v-if="cleanNote" class="inv-note">
            <span class="inv-note-lbl">Note:</span> {{ cleanNote }}
          </div>

          <!-- Footer -->
          <div class="inv-footer">
            <div class="inv-sig">
              <div class="inv-sig-line"></div>
              <div class="inv-sig-name">Member Signature</div>
            </div>
            <div class="inv-footer-mid">
              <div class="inv-generated">Generated on {{ nowDate }}</div>
              <div class="inv-receipt-note">This is a computer-generated receipt.</div>
            </div>
            <div class="inv-sig">
              <div class="inv-sig-line"></div>
              <div class="inv-sig-name">Authorized Signature</div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { site_settings_store } from "../../../../GlobalStore/site_settings_store";
import { mapActions } from "pinia";

export default {
  data: () => ({
    visible: false,
    item: {},
  }),

  computed: {
    orgName() {
      return this.get_setting_value('site_name') || 'Organization';
    },
    orgLogo() {
      const v = this.get_setting_value('image');
      if (!v) return null;
      return v.startsWith('http') || v.startsWith('/') ? v : '/' + v;
    },
    orgInitials() {
      return this.orgName.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
    },
    cleanNote() {
      if (!this.item.note) return '';
      return this.stripHtml(this.item.note).trim();
    },
    nowDate() {
      return new Date().toLocaleString('en-BD', { dateStyle: 'medium', timeStyle: 'short' });
    },
  },

  methods: {
    ...mapActions(site_settings_store, ['get_setting_value']),
    open(item) {
      this.item = { ...item };
      this.visible = true;
    },
    close() {
      this.visible = false;
    },
    fmt(v) {
      return Number(v || 0).toLocaleString('en-BD');
    },
    fmtDate(d) {
      if (!d) return '—';
      return new Date(d).toLocaleDateString('en-BD', { year: 'numeric', month: 'long', day: 'numeric' });
    },
    fmtMonth(d) {
      if (!d) return '—';
      return new Date(d).toLocaleDateString('en-BD', { year: 'numeric', month: 'long' });
    },
    stripHtml(html) {
      if (!html) return '';
      return String(html).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
    },
    printInvoice() {
      const el = document.getElementById('deposit-invoice-print');
      if (!el) return;
      const win = window.open('', '_blank', 'width=820,height=720');
      win.document.write(`<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Deposit Receipt - ${this.item.voucher_no || ''}</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:'Segoe UI',Arial,sans-serif;background:#fff;color:#1e293b;padding:40px 48px}
  .inv-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:22px}
  .inv-top__left{display:flex;align-items:center;gap:14px}
  .inv-logo{height:56px;width:56px;object-fit:contain;border-radius:8px;border:1px solid #e2e8f0}
  .inv-logo-initials{height:56px;width:56px;border-radius:8px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-size:22px;font-weight:800;display:flex;align-items:center;justify-content:center;letter-spacing:1px}
  .inv-org-name{font-size:20px;font-weight:800;color:#0f172a;line-height:1.2}
  .inv-org-sub{font-size:12px;color:#64748b;margin-top:4px}
  .inv-top__right{text-align:right}
  .inv-voucher{display:inline-block;padding:6px 18px;background:#f1f5f9;border:2px solid #6366f1;border-radius:8px;font-family:monospace;font-size:15px;font-weight:800;color:#4338ca;letter-spacing:1px;margin-bottom:6px}
  .inv-date{font-size:12px;color:#64748b}
  .inv-rule{border:none;border-top:2px solid #e2e8f0;margin:18px 0}
  .inv-cols{display:grid;grid-template-columns:1fr 1fr;gap:28px;margin-bottom:22px}
  .inv-col-title{font-size:10px;text-transform:uppercase;letter-spacing:1.2px;color:#94a3b8;font-weight:700;padding-bottom:6px;border-bottom:1px solid #f1f5f9;margin-bottom:10px}
  .inv-row-item{display:flex;align-items:baseline;gap:0;margin-bottom:7px}
  .inv-row-lbl{font-size:12.5px;color:#64748b;min-width:118px;flex-shrink:0}
  .inv-row-sep{font-size:12.5px;color:#94a3b8;margin:0 6px}
  .inv-row-val{font-size:13px;font-weight:600;color:#1e293b}
  .inv-chip{display:inline-block;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700}
  .inv-chip.inv-chip--purple{background:#ede9fe;color:#6d28d9}
  .inv-chip.inv-chip--blue{background:#dbeafe;color:#1d4ed8}
  .inv-amount-wrap{margin:18px 0}
  .inv-amount-box{background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border-radius:12px;padding:20px 28px;display:flex;justify-content:space-between;align-items:center}
  .inv-amount-lbl{font-size:13px;font-weight:600;opacity:.9}
  .inv-amount-val{font-size:28px;font-weight:900;letter-spacing:-0.5px}
  .inv-note{background:#f8fafc;border-left:3px solid #6366f1;padding:10px 14px;border-radius:0 6px 6px 0;font-size:12.5px;color:#475569;margin:18px 0}
  .inv-note-lbl{font-weight:700;color:#6366f1}
  .inv-footer{display:flex;justify-content:space-between;align-items:flex-end;margin-top:48px;padding-top:20px;border-top:1px solid #e2e8f0}
  .inv-sig{text-align:center;min-width:140px}
  .inv-sig-line{border-top:1.5px solid #334155;margin-bottom:7px}
  .inv-sig-name{font-size:11px;color:#64748b}
  .inv-footer-mid{text-align:center;flex:1}
  .inv-generated{font-size:11px;color:#94a3b8}
  .inv-receipt-note{font-size:10px;color:#cbd5e1;margin-top:3px}
</style></head><body>
${el.innerHTML}
</body></html>`);
      win.document.close();
      win.focus();
      setTimeout(() => { win.print(); win.close(); }, 400);
    },
  },
};
</script>

<style scoped>
.inv-backdrop {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(0, 0, 0, 0.65); backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center;
  padding: 16px;
}
.inv-modal {
  background: #fff; border-radius: 16px;
  width: 100%; max-width: 760px; max-height: 92vh;
  overflow-y: auto; box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4);
}
.inv-modal__header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 22px; border-bottom: 1px solid #e2e8f0;
  position: sticky; top: 0; background: #fff; z-index: 2;
}
.inv-modal__title { margin: 0; font-size: 14px; font-weight: 700; color: #334155; }

/* ── Invoice body ── */
.inv-body {
  padding: 32px 36px;
  background: #fff !important;
  color: #1e293b !important;
}

/* Header */
.inv-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 22px; }
.inv-top__left { display: flex; align-items: center; gap: 14px; }
.inv-logo-wrap { flex-shrink: 0; }
.inv-logo { height: 56px; width: 56px; object-fit: contain; border-radius: 8px; border: 1px solid #e2e8f0; }
.inv-logo-initials {
  height: 56px; width: 56px; border-radius: 8px; flex-shrink: 0;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; font-size: 20px; font-weight: 800;
  display: flex; align-items: center; justify-content: center; letter-spacing: 1px;
}
.inv-org-name { font-size: 20px; font-weight: 800; color: #0f172a !important; line-height: 1.2; }
.inv-org-sub { font-size: 12px; color: #64748b !important; margin-top: 4px; }
.inv-top__right { text-align: right; }
.inv-voucher {
  display: inline-block; padding: 6px 16px;
  background: #f1f5f9 !important; border: 2px solid #6366f1;
  border-radius: 8px; font-family: monospace; font-size: 14px;
  font-weight: 800; color: #4338ca !important; letter-spacing: 1px; margin-bottom: 6px;
}
.inv-date { font-size: 12px; color: #64748b !important; }

.inv-rule { border: none; border-top: 2px solid #e2e8f0; margin: 18px 0; }

/* Two-column info */
.inv-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; margin-bottom: 22px; }
.inv-col-block {
  background: #f8fafc !important;
  border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 18px;
}
.inv-col-title {
  font-size: 10px; text-transform: uppercase; letter-spacing: 1.2px;
  color: #94a3b8 !important; font-weight: 700;
  padding-bottom: 8px; border-bottom: 1px solid #e2e8f0; margin-bottom: 12px;
}
.inv-row-item { display: flex; align-items: baseline; margin-bottom: 8px; }
.inv-row-lbl { font-size: 12.5px; color: #64748b !important; min-width: 112px; flex-shrink: 0; }
.inv-row-sep { font-size: 12.5px; color: #94a3b8 !important; margin: 0 6px; }
.inv-row-val { font-size: 13px; font-weight: 600; color: #1e293b !important; }
.inv-chip {
  display: inline-block; padding: 2px 10px;
  border-radius: 20px; font-size: 11px; font-weight: 700;
}
.inv-chip--purple { background: #ede9fe !important; color: #6d28d9 !important; }
.inv-chip--blue   { background: #dbeafe !important; color: #1d4ed8 !important; }

/* Amount */
.inv-amount-wrap { margin: 18px 0; }
.inv-amount-box {
  background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
  color: #fff !important; border-radius: 12px; padding: 20px 28px;
  display: flex; justify-content: space-between; align-items: center;
}
.inv-amount-lbl { font-size: 13px; font-weight: 600; opacity: 0.9; color: #fff !important; }
.inv-amount-val { font-size: 27px; font-weight: 900; color: #fff !important; letter-spacing: -0.5px; }

/* Note */
.inv-note {
  background: #f0f4ff !important; border-left: 3px solid #6366f1;
  padding: 10px 14px; border-radius: 0 8px 8px 0;
  font-size: 12.5px; color: #3730a3 !important; margin: 18px 0;
}
.inv-note-lbl { font-weight: 700; color: #6366f1 !important; }

/* Footer */
.inv-footer {
  display: flex; justify-content: space-between; align-items: flex-end;
  margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 20px;
}
.inv-sig { text-align: center; min-width: 130px; }
.inv-sig-line { border-top: 1.5px solid #334155; margin-bottom: 7px; }
.inv-sig-name { font-size: 11px; color: #64748b !important; }
.inv-footer-mid { text-align: center; flex: 1; }
.inv-generated { font-size: 11px; color: #94a3b8 !important; }
.inv-receipt-note { font-size: 10px; color: #cbd5e1 !important; margin-top: 3px; }
</style>
