# 🤖 FlowSpec AI - Workflow Automation Platform

**REACTORYX Hackathon 2026: Openspec x AI Challenge** 🏆

Aplikasi AI Agent yang menggunakan **OpenSpec 1.0.0** sebagai standar untuk workflow automation, dikombinasikan dengan **Gemini AI** untuk natural language processing.

> **Innovation**: Natural Language → AI → OpenSpec Workflows → Automated Execution

## 🌟 OpenSpec Features

- ✅ **OpenAPI 3.0 Specification**: 665 lines, 15+ endpoints documented
- ✅ **OpenSpec 1.0.0 Workflows**: Auto-generated from natural language
- ✅ **Swagger UI**: Interactive API documentation at `/api/docs`
- ✅ **Workflow Generator**: 440 lines of OpenSpec-compliant workflow creation
- ✅ **Workflow Execution Engine**: Step-by-step execution with tracking
- ✅ **Workflow Validation**: OpenSpec compliance checker
- ✅ **Database Storage**: JSONB column for workflow specifications

📖 **[Complete OpenSpec Implementation Guide](./OPENSPEC_IMPLEMENTATION.md)**

## 🚀 Fitur Utama

- **NLP dengan Gemini AI**: Memproses perintah alami dalam Bahasa Indonesia & English
- **OpenSpec Workflows**: Setiap command menghasilkan OpenSpec 1.0.0 workflow
- **Integrasi Google Calendar**: Kelola jadwal dan pertemuan otomatis
- **Integrasi Gmail**: Kirim email pengingat otomatis
- **Integrasi Slack**: Notifikasi real-time ke channel tim
- **Task Management**: Kelola tugas dengan prioritas dan deadline
- **Reminder Otomatis**: Pengingat berbasis waktu
- **Scalable Architecture**: Kubernetes-ready dengan PostgreSQL

## 🏗️ Arsitektur Sistem

```
┌─────────────────┐
│   User Input    │ (Natural Language)
│  "Buat task..." │
└────────┬────────┘
         │
┌────────▼────────┐
│   Frontend      │ (React Dashboard)
│   Dashboard     │
└────────┬────────┘
         │
┌────────▼────────┐
│   Backend API   │ (Node.js/Express)
│   + OpenSpec    │
└────────┬────────┘
         │
    ┌────┴────┬──────────────┐
    │         │              │
┌───▼───┐ ┌──▼──────────┐ ┌─▼──────────┐
│Gemini │ │ WorkflowSpec│ │PostgreSQL  │
│  AI   │ │  Generator  │ │  (JSONB)   │
└───┬───┘ └──┬──────────┘ └────────────┘
    │        │
    └────┬───┘
         │
┌────────▼────────────────────┐
│  External Services          │
├────────────────────────────┤
│ • Google Calendar API      │
│ • Gmail API                │
│ • Slack API                │
│ • OpenSpec Execution       │
└────────────────────────────┘
```

### OpenSpec Workflow Flow
```
Natural Language Input
        ↓
Gemini AI (NLP Parsing)
        ↓
WorkflowSpec Generator (OpenSpec 1.0.0)
        ↓
PostgreSQL (Store JSONB)
        ↓
Workflow Executor
        ↓
Integrations (Email, Slack, Calendar)
```

## 📋 Prerequisites

- Node.js 18.x atau lebih tinggi
- PostgreSQL 14.x atau lebih tinggi
- Docker & Docker Compose
- Kubernetes (untuk production)
- API Keys:
  - OpenAI API Key
  - Google Cloud Console (Calendar & Gmail API)
  - Slack App Token

## 🛠️ Setup Development

### 1. Clone Repository

```bash
git clone https://github.com/musahabibulloh17/Lomba2026.git
cd Lomba2026
```

### 2. Setup Environment Variables

```bash
# Backend
cp backend/.env.example backend/.env

# Frontend
cp frontend/.env.example frontend/.env
```

Edit file `.env` dengan API keys Anda.

### 3. Install Dependencies

```bash
# Backend
cd backend
npm install

# Frontend
cd ../frontend
npm install
```

### 4. Setup Database

```bash
cd backend
npm run db:migrate
npm run db:seed
```

### 5. Run Development Server

```bash
# Terminal 1 - Backend
cd backend
npm run dev

# Terminal 2 - Frontend
cd frontend
npm start
```

Aplikasi akan berjalan di:
- Frontend: http://localhost:3000
- Backend API: http://localhost:5000

## 🐳 Docker Development

```bash
docker-compose up -d
```

## ☸️ Kubernetes Deployment

```bash
# Build images
docker build -t workflow-ai-backend:latest ./backend
docker build -t workflow-ai-frontend:latest ./frontend

# Deploy to Kubernetes
kubectl apply -f k8s/namespace.yaml
kubectl apply -f k8s/configmap.yaml
kubectl apply -f k8s/secrets.yaml
kubectl apply -f k8s/postgres.yaml
kubectl apply -f k8s/backend.yaml
kubectl apply -f k8s/frontend.yaml
kubectl apply -f k8s/ingress.yaml
```

## 📖 API Documentation

### OpenSpec Endpoints

#### 1. Natural Language Processing
```bash
POST /api/nlp/process
Content-Type: application/json

{
  "command": "Buat task urgent presentasi besok jam 2"
}
```

**Response** (with OpenSpec workflow):
```json
{
  "success": true,
  "intent": "create_task",
  "entities": {
    "title": "presentasi",
    "priority": "urgent",
    "dueDate": "2026-02-09T14:00:00.000Z"
  },
  "workflowSpec": {
    "openspec": "1.0.0",
    "workflow": {
      "id": "task-workflow-1738234567890",
      "name": "Task Creation Workflow",
      "version": "1.0.0",
      "steps": [
        {
          "id": "parse_intent",
          "name": "Parse User Intent",
          "type": "nlp_processing",
          "status": "completed"
        },
        {
          "id": "create_task",
          "name": "Create Task in Database",
          "type": "database_operation",
          "status": "pending"
        },
        {
          "id": "send_notifications",
          "name": "Send Notifications",
          "type": "notification",
          "status": "pending"
        }
      ],
      "integrations": [
        {
          "name": "email",
          "type": "notification",
          "enabled": true
        },
        {
          "name": "slack",
          "type": "messaging",
          "enabled": true
        }
      ]
    }
  },
  "response": "✅ Task berhasil dibuat"
}
```

#### 2. Get Workflow Specification
```bash
GET /api/workflow/spec/:commandId
```

#### 3. Validate Workflow
```bash
POST /api/workflow/validate
Content-Type: application/json

{
  "workflowSpec": { /* OpenSpec workflow */ }
}
```

#### 4. Execute Workflow
```bash
POST /api/workflow/execute
Content-Type: application/json

{
  "workflowSpec": { /* OpenSpec workflow */ },
  "context": {
    "userId": "uuid"
  }
}
```

#### 5. Get Workflow Templates
```bash
GET /api/workflow/templates
```

### Interactive Documentation

**Swagger UI**: http://localhost:5000/api/docs

Features:
- Try all endpoints directly from browser
- View OpenSpec schemas
- Test authentication
- See real-time responses

### All Endpoints:

### All Endpoints:

#### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user
- `POST /api/auth/google` - Google OAuth login
- `GET /api/auth/me` - Get current user

#### Natural Language Processing
- `POST /api/nlp/process` - Process command (returns OpenSpec workflow)
- `GET /api/nlp/history` - Get NLP command history

#### Workflow (OpenSpec)
- `GET /api/workflow/spec/:commandId` - Get workflow specification
- `POST /api/workflow/validate` - Validate OpenSpec workflow
- `POST /api/workflow/execute` - Execute workflow
- `GET /api/workflow/templates` - Get workflow templates

#### Tasks
- `GET /api/tasks` - Get all tasks
- `POST /api/tasks` - Create new task
- `PUT /api/tasks/:id` - Update task
- `DELETE /api/tasks/:id` - Delete task

#### Meetings
- `GET /api/meetings` - Get meetings
- `POST /api/meetings` - Create meeting
- `PUT /api/meetings/:id` - Update meeting
- `DELETE /api/meetings/:id` - Delete meeting

#### Notifications
- `POST /api/email/send` - Send email
- `POST /api/slack/notify` - Send Slack notification

## 🔒 Security

- JWT Authentication
- OAuth2 untuk Google APIs
- Encrypted environment variables
- Rate limiting pada API endpoints
- Input validation dan sanitization

## 🧪 Testing

```bash
# Backend tests
cd backend
npm test

# Frontend tests
cd frontend
npm test
```

## 📊 Monitoring

- Health check endpoint: `/api/health`
- Metrics endpoint: `/api/metrics`
- Logs: Structured logging dengan Winston

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

MIT License

## 👥 Team

Developed for Lomba 2026

## 📞 Support

Untuk bantuan dan pertanyaan, silakan buka issue di GitHub repository.
