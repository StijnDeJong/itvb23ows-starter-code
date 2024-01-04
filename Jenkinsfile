// Jenkinsfile (Declarative Pipeline)
/* Requires the Docker Pipeline plugin */
pipeline {
    agent {
        docker {
            image 'docker:20.10'
        }
    }

    stages {
        stage('Build') {
            steps {
                // Your build steps
                echo "Building..."
            }
        }

        stage('Test') {
            steps {
                // Your testing steps
                echo "Testing..."
            }
        }

        stage('Deploy') {
            steps {
                // Use Docker Compose with Docker Pipeline plugin
                echo "Deploying..."
                script {
                    dockerDeploy()
                }
            }
        }
    }
}
