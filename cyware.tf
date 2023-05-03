# Define provider
provider "aws" {
  region = "us-west-2"  
}

# Create VPC
resource "aws_vpc" "my_vpc" {
  cidr_block = "10.0.0.0/16"  
}

# Create public subnet
resource "aws_subnet" "public_subnet" {
  vpc_id     = aws_vpc.my_vpc.id
  cidr_block = "10.0.1.0/24"  
  availability_zone = "us-west-2a"
}

# Create private subnet
resource "aws_subnet" "private_subnet" {
  vpc_id     = aws_vpc.my_vpc.id
  cidr_block = "10.0.2.0/24" 
  availability_zone = "us-west-2b" 
}

# Create security groups
resource "aws_security_group" "public_sg" {
  name_prefix = "public_sg_"

  ingress {
    from_port = 80
    to_port   = 80
    protocol  = "tcp"
    cidr_blocks = ["0.0.0.0/0"]  
  }
}

resource "aws_security_group" "private_sg" {
  name_prefix = "private_sg_"

  ingress {
    from_port = 22
    to_port   = 22
    protocol  = "tcp"
    cidr_blocks = ["0.0.0.0/0"]  
  }

  ingress {
    from_port = 80
    to_port   = 80
    protocol  = "tcp"
    security_groups = [aws_security_group.public_sg.id]
  }
}

# Create EC2 instance in private subnet
resource "aws_instance" "ec2_instance" {
  ami           = "ami-0c55b159cbfafe1f0" 
  instance_type = "t2.micro" 
  subnet_id     = aws_subnet.private_subnet.id
  vpc_security_group_ids = [aws_security_group.private_sg.id]

  connection {
    type     = "ssh"
    user     = "ec2-user"
    private_key = file("~/.ssh/id_rsa")  
    timeout  = "2m"
  }

# To run user data when creating ec2 instance 
#  provisioner "remote-exec" {
#    inline = [
#      "sudo yum update -y",
#      "sudo yum install -y httpd",
#      "sudo echo 'Hello, World!' > /var/www/html/index.html",
#      "sudo systemctl enable httpd",
#      "sudo systemctl start httpd"
#    ]
#  }
#}

# Create ALB in Public subnet
resource "aws_lb" "my_lb" {
  name               = "my-lb"
  internal           = false
  load_balancer_type = "application"

  security_groups = [aws_security_group.my_sg.id]

  subnets = [
    aws_subnet.public_subnet.id,
  ]

  tags = {
    Name = "my-lb"
  }
}

resource "aws_lb_target_group" "my_lb_target_group" {
  name_prefix = "my-tg"
  port        = 80
  protocol    = "HTTP"
  vpc_id      = aws_vpc.my_vpc.id

  health_check {
    healthy_threshold   = 2
    unhealthy_threshold = 2
    timeout             = 3
    interval            = 30
    path                = "/"
  }
}

resource "aws_lb_listener" "my_lb_listener" {
  load_balancer_arn = aws_lb.my_lb.arn
  port              = 80
  protocol          = "HTTP"

  default_action {
    target_group_arn = aws_lb_target_group.my_lb_target_group.arn
    type             = "forward"
  }
}