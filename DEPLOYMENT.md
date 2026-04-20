# 🚀 Panduan Deployment

## Prerequisites

- Docker & Docker Compose
- Kubernetes cluster (untuk production)
- PostgreSQL database
- API Keys:
  - OpenAI API Key
  - Google Cloud Console credentials
  - Slack Bot Token

## Development Setup

### 1. Clone Repository

```bash
git clone https://github.com/musahabibulloh17/Lomba2026.git
cd Lomba2026
```

### 2. Setup Environment Variables

#### Backend (.env)
```bash
cd backend
cp .env.example .env
```

Edit `backend/.env` dan isi dengan API keys Anda:
- `OPENAI_API_KEY`
- `GOOGLE_CLIENT_ID` & `GOOGLE_CLIENT_SECRET`
- `SLACK_BOT_TOKEN`
- `JWT_SECRET` (generate random string)
- `DB_PASSWORD`

#### Frontend (.env)
```bash
cd frontend
cp .env.example .env
```

### 3. Run with Docker Compose

```bash
# Dari root directory
docker-compose up -d
```

Aplikasi akan berjalan di:
- Frontend: http://localhost:3000
- Backend API: http://localhost:5000
- PostgreSQL: localhost:5432

### 4. Initialize Database

```bash
# Install dependencies
cd backend
npm install

# Run migrations
npm run db:migrate

# (Optional) Seed data
npm run db:seed
```

## Production Deployment dengan Kubernetes

### 1. Build Docker Images

```bash
# Build backend
docker build -t your-registry/workflow-ai-backend:latest ./backend

# Build frontend
docker build -t your-registry/workflow-ai-frontend:latest ./frontend

# Push to registry
docker push your-registry/workflow-ai-backend:latest
docker push your-registry/workflow-ai-frontend:latest
```

### 2. Update Kubernetes Configs

Edit file `k8s/secrets.yaml` dengan credentials production Anda:

```bash
kubectl create secret generic workflow-ai-secrets \
  --from-literal=DB_PASSWORD='your-password' \
  --from-literal=JWT_SECRET='your-jwt-secret' \
  --from-literal=OPENAI_API_KEY='sk-your-key' \
  --from-literal=GOOGLE_CLIENT_ID='your-client-id' \
  --from-literal=GOOGLE_CLIENT_SECRET='your-secret' \
  --from-literal=SLACK_BOT_TOKEN='xoxb-your-token' \
  -n workflow-ai
```

### 3. Deploy to Kubernetes

```bash
# Create namespace
kubectl apply -f k8s/namespace.yaml

# Apply configs
kubectl apply -f k8s/configmap.yaml
kubectl apply -f k8s/secrets.yaml

# Deploy database
kubectl apply -f k8s/postgres.yaml

# Wait for postgres to be ready
kubectl wait --for=condition=ready pod -l app=postgres -n workflow-ai --timeout=300s

# Deploy backend
kubectl apply -f k8s/backend.yaml

# Deploy frontend
kubectl apply -f k8s/frontend.yaml

# Setup ingress
kubectl apply -f k8s/ingress.yaml
```

### 4. Verify Deployment

```bash
# Check all pods
kubectl get pods -n workflow-ai

# Check services
kubectl get svc -n workflow-ai

# Check ingress
kubectl get ingress -n workflow-ai

# View logs
kubectl logs -f deployment/backend -n workflow-ai
kubectl logs -f deployment/frontend -n workflow-ai
```

### 5. Setup Domain & SSL

Update `k8s/ingress.yaml` dengan domain Anda:

```yaml
spec:
  tls:
  - hosts:
    - your-domain.com
    - api.your-domain.com
    secretName: workflow-ai-tls
  rules:
  - host: your-domain.com
    # ...
  - host: api.your-domain.com
    # ...
```

Install cert-manager untuk SSL otomatis:

```bash
kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.13.0/cert-manager.yaml
```

## Monitoring & Logs

### View Application Logs

```bash
# Backend logs
kubectl logs -f deployment/backend -n workflow-ai

# Frontend logs
kubectl logs -f deployment/frontend -n workflow-ai

# Database logs
kubectl logs -f deployment/postgres -n workflow-ai
```

### Check Resource Usage

```bash
kubectl top pods -n workflow-ai
kubectl top nodes
```

### Health Checks

```bash
# Backend health
curl http://your-api-domain.com/health

# Frontend health
curl http://your-domain.com/health
```

## Scaling

### Manual Scaling

```bash
# Scale backend
kubectl scale deployment/backend --replicas=5 -n workflow-ai

# Scale frontend
kubectl scale deployment/frontend --replicas=3 -n workflow-ai
```

### Auto-scaling

HPA (Horizontal Pod Autoscaler) sudah dikonfigurasi di `k8s/backend.yaml`:

```bash
# Check HPA status
kubectl get hpa -n workflow-ai

# Describe HPA
kubectl describe hpa backend-hpa -n workflow-ai
```

## Backup & Restore

### Database Backup

```bash
# Backup database
kubectl exec -n workflow-ai deployment/postgres -- pg_dump -U postgres workflow_ai_db > backup.sql

# Restore database
kubectl exec -i -n workflow-ai deployment/postgres -- psql -U postgres workflow_ai_db < backup.sql
```

## Troubleshooting

### Pod tidak running

```bash
kubectl describe pod <pod-name> -n workflow-ai
kubectl logs <pod-name> -n workflow-ai
```

### Database connection error

```bash
# Check postgres service
kubectl get svc postgres-service -n workflow-ai

# Check postgres logs
kubectl logs deployment/postgres -n workflow-ai

# Test connection
kubectl exec -it deployment/backend -n workflow-ai -- nc -zv postgres-service 5432
```

### API tidak bisa diakses

```bash
# Check backend pods
kubectl get pods -l app=backend -n workflow-ai

# Check backend logs
kubectl logs deployment/backend -n workflow-ai

# Check service
kubectl get svc backend-service -n workflow-ai
```

## Environment Variables Reference

### Backend Required Variables

- `OPENAI_API_KEY` - OpenAI API key untuk NLP processing
- `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` - PostgreSQL credentials
- `JWT_SECRET` - Secret key untuk JWT authentication
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` - Google OAuth credentials
- `SLACK_BOT_TOKEN` - Slack bot token

### Optional Variables

- `NODE_ENV` - Environment (development/production)
- `PORT` - Backend port (default: 5000)
- `CORS_ORIGIN` - Frontend URL
- `LOG_LEVEL` - Logging level (info/debug/error)
- `RATE_LIMIT_MAX_REQUESTS` - Rate limit per window
- `ENABLE_REMINDERS` - Enable cron jobs (true/false)

## Security Best Practices

1. **Never commit secrets** - Use Kubernetes secrets or environment variables
2. **Use SSL/TLS** - Always use HTTPS in production
3. **Enable authentication** - JWT tokens dengan expiration
4. **Rate limiting** - Aktif secara default
5. **Input validation** - Express-validator untuk semua input
6. **Database security** - PostgreSQL dengan password yang kuat
7. **CORS configuration** - Set CORS_ORIGIN ke domain yang valid

## Support

Untuk bantuan lebih lanjut, buka issue di GitHub repository atau hubungi tim support.
