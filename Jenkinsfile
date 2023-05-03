pipeline {
    agent any
    
    environment {
        DOCKER_VERSION = '20.10.11'
        DOCKER_COMPOSE_VERSION = '1.29.2'
        SSH_KEY_NAME = 'my-ssh-key'
        EC2_INSTANCE_PUBLIC_IP = 'EC2_INSTANCE_PUBLIC_IP_HERE'
    }
    
    stages {
        stage('Install Docker and Docker Compose') {
            steps {
                withCredentials([sshUserPrivateKey(credentialsId: SSH_KEY_NAME, keyFileVariable: 'SSH_KEY_FILE')]) {
                    sh """
                        ssh -o StrictHostKeyChecking=no -i $SSH_KEY_FILE ec2-user@${EC2_INSTANCE_PUBLIC_IP} '
                            sudo yum update -y &&
                            sudo amazon-linux-extras install docker &&
                            sudo systemctl start docker &&
                            sudo usermod -aG docker ec2-user &&
                            sudo curl -L \"https://github.com/docker/compose/releases/download/${DOCKER_COMPOSE_VERSION}/docker-compose-$(uname -s)-$(uname -m)\" -o /usr/local/bin/docker-compose &&
                            sudo chmod +x /usr/local/bin/docker-compose
                        '
                    """
                }
            }
        }
        
        stage('Copy docker-compose.yml to EC2 instance') {
            steps {
                withCredentials([sshUserPrivateKey(credentialsId: SSH_KEY_NAME, keyFileVariable: 'SSH_KEY_FILE')]) {
                    sh "scp -o StrictHostKeyChecking=no -i $SSH_KEY_FILE docker-compose.yml ec2-user@${EC2_INSTANCE_PUBLIC_IP}:~/"
                }
            }
        }
        
        stage('Run docker-compose up') {
            steps {
                withCredentials([sshUserPrivateKey(credentialsId: SSH_KEY_NAME, keyFileVariable: 'SSH_KEY_FILE')]) {
                    sh "ssh -o StrictHostKeyChecking=no -i $SSH_KEY_FILE ec2-user@${EC2_INSTANCE_PUBLIC_IP} 'cd ~/ && docker-compose up -d'"
                }
            }
        }
    }
}