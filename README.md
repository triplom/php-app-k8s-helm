# Deploying a PHP Application on Kubernetes with Helm

A PHP-FPM application with Nginx reverse proxy and MariaDB database, containerised and deployable on Kubernetes via Helm 3.

## Architecture

```
[Client] → [Nginx Service (NodePort:8080)]
              → [Nginx Pod (bitnami/nginx:1.25)]
                  → FastCGI → [PHP-FPM Pod (bitnami/php-fpm:8.2)]
[MariaDB Pod] ← [PHP-FPM]
```

## Prerequisites

- Docker and Docker Compose
- kubectl configured for your cluster
- Helm 3.x
- A Docker Hub account (to push your image)

---

## Step 1: Obtain the Source Code

```bash
git clone https://github.com/triplom/php-app-k8s-helm.git
cd php-app-k8s-helm
```

---

## Step 2: Configure Environment Variables

Copy the example env file and fill in your credentials:

```bash
cp .env.example .env
# Edit .env with your database credentials (never commit this file)
```

---

## Step 3: Build the Docker Image

Replace `USERNAME` with your Docker Hub ID:

```bash
docker build . -t USERNAME/phpfpm-app:0.2.2
```

---

## Step 4: Run Locally with Docker Compose

```bash
docker-compose up -d
```

The application is available at <http://localhost:8080>.

---

## Step 5: Push the Docker Image

```bash
docker login
docker push USERNAME/phpfpm-app:0.2.2
```

Update `helm-chart/values.yaml` to reference your image:

```yaml
image:
  repository: USERNAME/phpfpm-app
  tag: "0.2.2"
```

---

## Step 6: Deploy on Kubernetes with Helm

Verify cluster connectivity:

```bash
kubectl cluster-info
```

Add the Bitnami chart repository and fetch dependencies:

```bash
helm repo add bitnami https://charts.bitnami.com/bitnami
helm repo update
helm dependency update helm-chart/
```

Create your secret values file (**never commit this**):

```bash
cp helm-chart/helm-values-secret.yaml.example helm-chart/helm-values-secret.yaml
# Edit helm-chart/helm-values-secret.yaml with real database passwords
```

Install the chart:

```bash
helm upgrade --install phpfpm helm-chart/ \
  --values helm-chart/helm-values-secret.yaml \
  --wait
```

---

## Step 7: Get the Application URL

**Minikube:**

```bash
minikube service phpfpm-php-app-nginx --url
```

**Other environments:**

```bash
export NODE_PORT=$(kubectl get svc phpfpm-php-app-nginx -o jsonpath="{.spec.ports[0].nodePort}")
export NODE_IP=$(kubectl get nodes -o jsonpath="{.items[0].status.addresses[0].address}")
echo "http://$NODE_IP:$NODE_PORT"
```

---

## Clean Up

```bash
helm uninstall phpfpm
```

---

## Security Notes

- Database credentials are managed via environment files and Helm secret values — never hardcoded.
- PHP-FPM is exposed only as `ClusterIP` (internal to the cluster); only Nginx is exposed externally.
- Nginx enforces security headers (X-Frame-Options, X-Content-Type-Options, etc.).
- Only known PHP entry points (`index.php`, `app_version.php`) are executed; all other `.php` files return 403.
- All Docker images are pinned to specific versions for reproducible builds.
