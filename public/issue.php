<?php
require_once __DIR__ . '/bootstrap.php';

use Core\Session;

Session::start();

if (!Session::get('access_token')) {
    header('Location: /api/login.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>GitHub Issue Tracker</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            background: #f5f7fa;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        button {
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }

        .primary { background: #2563eb; color: white; }
        .danger { background: #dc2626; color: white; }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            text-align: left;
            background: #f3f4f6;
            padding: 12px;
            font-size: 13px;
            text-transform: uppercase;
        }

        td {
            padding: 12px;
            border-top: 1px solid #e5e7eb;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .low { background: #dcfce7; color: #166534; }
        .medium { background: #fef9c3; color: #854d0e; }
        .high { background: #fee2e2; color: #991b1b; }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>

<div id="app" class="container"></div>

<script>
    const { createApp } = Vue;

    const app = createApp({
        data() {
            return {
                issues: [],
                loading: true,
                error: null,
                search: '',
                showModal: false,
                modalContent: '',
                currentPage: 1,
                perPage: 5
            }
        },

        computed: {

            filteredIssues() {
                if (!this.search) return this.issues;

                return this.issues.filter(i =>
                    i.title.toLowerCase().includes(this.search.toLowerCase()) ||
                    (i.body && i.body.toLowerCase().includes(this.search.toLowerCase()))
                );
            },

            totalPages() {
                return Math.ceil(this.filteredIssues.length / this.perPage);
            },

            paginatedIssues() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return this.filteredIssues.slice(start, end);
            }
        },

        watch: {
            search() {
                this.currentPage = 1;
            },
            perPage() {
                this.currentPage = 1;
            }
        },

        mounted() {
            this.fetchIssues();
        },

        methods: {

            async fetchIssues() {
                try {
                    const response = await fetch('/api/issues.php', {
                        credentials: 'include'
                    });

                    if (!response.ok) throw new Error('Failed to load issues');

                    this.issues = await response.json();
                } catch (err) {
                    this.error = err.message;
                } finally {
                    this.loading = false;
                }
            },

            async openCreateModal() {
                const response = await fetch('/create.php', {
                    credentials: 'include'
                });

                this.modalContent = await response.text();
                this.showModal = true;

                this.$nextTick(() => {
                    const scripts = document.querySelectorAll('.modal-overlay script');
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        newScript.textContent = oldScript.textContent;
                        document.body.appendChild(newScript);
                    });
                });
            },

            closeModal() {
                this.showModal = false;
                this.modalContent = '';
            }
        },

        template: `
      <div>

        <div class="header">
          <h1>GitHub Issues</h1>

          <div class="actions">
            <button class="primary" @click="openCreateModal">
                Create Issue
            </button>
          </div>
        </div>

        <input v-model="search" placeholder="Search issues..." />

        <div v-if="loading">Loading issues...</div>
        <div v-if="error" style="color:red;">{{ error }}</div>

        <table v-if="!loading && filteredIssues.length">
          <thead>
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Description</th>
              <th>Client</th>
              <th>Priority</th>
              <th>Type</th>
              <th>Assigned</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          <tr v-for="issue in paginatedIssues" :key="issue.number">
              <td>{{ issue.number }}</td>
              <td>{{ issue.title }}</td>
              <td style="max-width:300px; white-space: pre-wrap;">
                {{ issue.body }}
              </td>
              <td>{{ issue.client }}</td>
              <td>
                <span class="badge" :class="issue.priority?.toLowerCase()">
                  {{ issue.priority }}
                </span>
              </td>
              <td>{{ issue.type }}</td>
              <td>{{ issue.assigned_to || '-' }}</td>
              <td>{{ issue.status }}</td>
            </tr>
          </tbody>
        </table>
        <div v-if="totalPages > 1" style="margin-top:20px; display:flex; justify-content:space-between; align-items:center;">

          <div>
            <label>Rows per page:</label>
            <select v-model.number="perPage" style="margin-left:5px;">
              <option :value="5">5</option>
              <option :value="10">10</option>
              <option :value="20">20</option>
            </select>
          </div>

          <div style="display:flex; gap:5px; align-items:center;">

            <button
                :disabled="currentPage === 1"
                @click="currentPage--">
              Prev
            </button>

            <button
                v-for="page in totalPages"
                :key="page"
                @click="currentPage = page"
                :style="{
                background: currentPage === page ? '#2563eb' : '#e5e7eb',
                color: currentPage === page ? 'white' : 'black'
            }">
              {{ page }}
            </button>

            <button
                :disabled="currentPage === totalPages"
                @click="currentPage++">
              Next
            </button>

          </div>
        </div>

        <div v-if="!loading && filteredIssues.length === 0">
          No issues found.
        </div>

        <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
            <div v-html="modalContent"></div>
        </div>

      </div>
    `
    }).mount('#app');

    window.app = app;
</script>

</body>
</html>