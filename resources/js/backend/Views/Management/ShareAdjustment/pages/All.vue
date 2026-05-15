<template>
  <div>
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h5>Share Adjustment History</h5>
        <router-link
          :to="{ name: 'CreateShareAdjustment' }"
          class="btn btn-primary btn-sm"
        >
          <i class="fa fa-plus mr-1"></i> New Adjustment
        </router-link>
      </div>

      <div class="card-body">
        <div class="d-flex mb-3" style="gap:8px">
          <input
            v-model="search"
            class="form-control"
            placeholder="Search member or note..."
            @input="onSearchInput"
          />
          <button class="btn btn-outline-secondary" @click="fetchData">Reload</button>
        </div>

        <div v-if="loading" class="text-center py-5"><i class="fa fa-spinner fa-spin"></i> Loading...</div>

        <div v-else>
          <div v-if="rows.length === 0" class="text-center py-5 text-muted">
            <i class="fa fa-inbox" style="font-size:32px;"></i>
            <p class="mt-2">No adjustments yet.</p>
          </div>

          <div v-else class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Date</th>
                  <th>Member</th>
                  <th>Type</th>
                  <th>From → To</th>
                  <th>Months</th>
                  <th class="text-right">Amount</th>
                  <th>Destination</th>
                  <th>Note</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(r, i) in rows" :key="r.id">
                  <td>{{ (page - 1) * perPage + i + 1 }}</td>
                  <td>{{ fmtDate(r.created_at) }}</td>
                  <td>{{ r.member_name || '—' }}</td>
                  <td>
                    <span class="badge" :class="r.adjustment_type === 'increase' ? 'badge-warning' : 'badge-info'">
                      <i :class="r.adjustment_type === 'increase' ? 'fa fa-arrow-up' : 'fa fa-arrow-down'"></i>
                      {{ r.adjustment_type }}
                    </span>
                  </td>
                  <td><strong>{{ r.from_shares }}</strong> → <strong>{{ r.to_shares }}</strong></td>
                  <td>{{ r.months_elapsed }}</td>
                  <td class="text-right">
                    <strong :class="r.adjustment_type === 'increase' ? 'text-danger' : 'text-success'">
                      ৳ {{ fmt(r.adjustment_amount) }}
                    </strong>
                  </td>
                  <td>
                    <span v-if="r.adjustment_type === 'decrease'" class="text-muted">
                      {{ r.refund_destination === 'extra_savings' ? 'Extra Savings' : 'Withdrawal' }}
                    </span>
                    <span v-else>—</span>
                  </td>
                  <td class="text-muted" style="max-width:200px;font-size:12px;">{{ r.note || '—' }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="total > perPage" class="d-flex justify-content-between mt-3">
            <span class="text-muted">Showing {{ rows.length }} of {{ total }}</span>
            <div>
              <button class="btn btn-sm btn-outline-secondary mr-1" :disabled="page <= 1" @click="changePage(page - 1)">
                <i class="fa fa-chevron-left"></i>
              </button>
              <span class="mx-2">Page {{ page }} / {{ totalPages }}</span>
              <button class="btn btn-sm btn-outline-secondary" :disabled="page >= totalPages" @click="changePage(page + 1)">
                <i class="fa fa-chevron-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data: () => ({
    rows: [],
    total: 0,
    page: 1,
    perPage: 20,
    search: "",
    loading: false,
    _searchTimer: null,
  }),

  computed: {
    totalPages() {
      return Math.max(1, Math.ceil(this.total / this.perPage));
    },
  },

  created() {
    this.fetchData();
  },

  methods: {
    fmt(n) {
      return Number(n || 0).toLocaleString("en-BD", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },
    fmtDate(d) {
      if (!d) return "—";
      return new Date(d).toLocaleDateString("en-BD", { year: "numeric", month: "short", day: "2-digit" });
    },

    async fetchData() {
      this.loading = true;
      try {
        const res = await axios.get(
          `${location.origin}/api/v1/share-adjustments`,
          { params: { page: this.page, limit: this.perPage, search_key: this.search } }
        );
        const d = res.data?.data ?? {};
        this.rows  = d.data ?? [];
        this.total = d.active_data_count ?? 0;
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },

    onSearchInput() {
      clearTimeout(this._searchTimer);
      this._searchTimer = setTimeout(() => {
        this.page = 1;
        this.fetchData();
      }, 300);
    },

    changePage(p) {
      this.page = p;
      this.fetchData();
    },
  },
};
</script>
