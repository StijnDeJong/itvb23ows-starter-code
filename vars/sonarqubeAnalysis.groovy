def call() {
    def PROJECT_KEY = 'project1'
    def SONAR_SCANNER_IMAGE = 'sonarsource/sonar-scanner-cli:latest'
    def scannerHome = tool 'SonarQube Scanner'
    def SONAR_TOKEN = credentials('SONAR_TOKEN')

    docker.image(SONAR_SCANNER_IMAGE).inside {
        withCredentials([string(credentialsId: 'SONAR_TOKEN', variable: 'SONAR_TOKEN')]) {
            sh """
                export SONAR_HOST_URL=http://host.docker.internal:9000
                export SONAR_LOGIN=${SONAR_TOKEN}
                ${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=${PROJECT_KEY} -Dsonar.host.url=${SONAR_HOST_URL} -Dsonar.login=${SONAR_LOGIN}
            """
        }
    }
}
