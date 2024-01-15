def call() {
    def PROJECT_KEY = 'project1'
    def SONAR_SCANNER_IMAGE = 'sonarsource/sonar-scanner-cli:latest'

    node {
        withCredentials([string(credentialsId: 'SONAR_TOKEN', variable: 'SONAR_TOKEN')]) {
            docker.image(SONAR_SCANNER_IMAGE).inside {
                withSonarQubeEnv('SonarQubeServer') {
                    sh "sonar-scanner -Dsonar.projectKey=${PROJECT_KEY} -Dsonar.host.url=http://localhost:9000 -Dsonar.login=\${SONAR_TOKEN}"
                }
            }
        }
    }
}