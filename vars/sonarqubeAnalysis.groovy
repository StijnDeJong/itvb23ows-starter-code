def call() {
    def PROJECT_KEY = 'project1'
    def SONAR_SCANNER_IMAGE = 'sonarsource/sonar-scanner-cli:latest'
    def scannerHome = tool 'SonarQube Scanner'
    def SONAR_TOKEN = credentials('SONAR_TOKEN')

    docker.image(SONAR_SCANNER_IMAGE).inside {
        withCredentials([string(credentialsId: 'SONAR_TOKEN', variable: 'SONAR_TOKEN')]) {
            sh """
                docker run -e SONAR_HOST_URL=http://host.docker.internal:9000 \
                           -e SONAR_LOGIN=${SONAR_TOKEN} \
                           -v ${scannerHome}/bin/sonar-scanner:/sonar-scanner/bin/sonar-scanner \
                           sonarsource/sonar-scanner-cli:latest \
                           -Dsonar.projectKey=${PROJECT_KEY} -Dsonar.login=${SONAR_TOKEN} -Dsonar.host.url=${SONAR_HOST_URL}
            """
        }
    }
}
