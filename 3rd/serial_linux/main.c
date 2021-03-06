#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <termios.h>
#include <string.h>
#include <fcntl.h>
#include <curl/curl.h>

#define FALSE -1
#define TRUE 0

char *usb_dev = "/dev/ttyUSB0";
char *post_url = "http://47.94.165.17:81/other/serial/serial.php";

int speed_arr[] =
        { B38400, B19200, B9600, B4800, B2400, B1200, B300, B38400, B9600,
          B4800, B2400, B1200, B300, };

int name_arr[] =
        { 38400, 19200, 9600, 4800, 2400, 1200, 300, 38400, 9600, 4800, 2400,
          1200, 300, };

void set_speed (int fd, int speed)
{
    int i;
    int status;
    struct termios Opt = {0};
    tcgetattr (fd, &Opt);
    for (i = 0; i < sizeof (speed_arr) / sizeof (int); i++)
    {
        if (speed == name_arr[i])
        {
            tcflush (fd, TCIOFLUSH);
            cfsetispeed (&Opt, speed_arr[i]);
            cfsetospeed (&Opt, speed_arr[i]);
            status = tcsetattr (fd, TCSANOW, &Opt);
            if (status != 0)
            {
                perror ("tcsetattr fd1");
                return;
            }
            tcflush (fd, TCIOFLUSH);
        }
    }
}

int set_Parity (int fd, int databits, int stopbits, int parity)
{
    struct termios options = {0};
    if (tcgetattr (fd, &options) != 0)
    {
        perror ("SetupSerial 1");
        return (FALSE);
    }
    options.c_cflag &= ~CSIZE;
    switch (databits)
    {
        case 7:
            options.c_cflag |= CS7;
            break;
        case 8:
            options.c_cflag |= CS8;
            break;
        default:
            fprintf (stderr, "Unsupported data size\n");
            return (FALSE);
    }
    switch (parity)
    {
        case 'n':
        case 'N':
            options.c_cflag &= ~PARENB;	/* Clear parity enable */
            options.c_iflag &= ~INPCK;	/* Enable parity checking */
            break;
        case 'o':
        case 'O':
            options.c_cflag |= (PARODD | PARENB);
            options.c_iflag |= INPCK;	/* Disnable parity checking */
            break;
        case 'e':
        case 'E':
            options.c_cflag |= PARENB;	/* Enable parity */
            options.c_cflag &= ~PARODD;
            options.c_iflag |= INPCK;	/* Disnable parity checking */
            break;
        case 'S':
        case 's':			/*as no parity */
            options.c_cflag &= ~PARENB;
            options.c_cflag &= ~CSTOPB;
            break;
        default:
            fprintf (stderr, "Unsupported parity\n");
            return (FALSE);
    }

    switch (stopbits)
    {
        case 1:
            options.c_cflag &= ~CSTOPB;
            break;
        case 2:
            options.c_cflag |= CSTOPB;
            break;
        default:
            fprintf (stderr, "Unsupported stop bits\n");
            return (FALSE);
    }
    /* Set input parity option */
    if (parity != 'n')
        options.c_iflag |= INPCK;
    tcflush (fd, TCIFLUSH);
    options.c_cc[VTIME] = 150;
    options.c_cc[VMIN] = 0;	/* Update the options and do it NOW */
    if (tcsetattr (fd, TCSANOW, &options) != 0)
    {
        perror ("SetupSerial 3");
        return (FALSE);
    }
    return (TRUE);
}


void post_data(const char *data)
{
    CURL *curl;
    CURLcode res;
    char post_arg[1024] = {0};

    sprintf(post_arg, "data=%s", data);

    curl_global_init(CURL_GLOBAL_ALL);

    curl = curl_easy_init();
    if(curl) {
	printf("post:[%s]\n", data);
        curl_easy_setopt(curl, CURLOPT_URL, post_url);
        curl_easy_setopt(curl, CURLOPT_POSTFIELDS, post_arg);

        /* Perform the request, res will get the return code */
        res = curl_easy_perform(curl);
        /* Check for errors */
        if(res != CURLE_OK)
            fprintf(stderr, "curl_easy_perform() failed: %s\n",
                    curl_easy_strerror(res));

        /* always cleanup */
        curl_easy_cleanup(curl);
    }
    curl_global_cleanup();
}

void readTTY()
{
    while(1)
    {
        int fd;

        while((fd = open(usb_dev, O_RDWR)) == -1)
        {
            perror("serial port error\n");
            sleep(1);
        }
        printf("open %s successfully\n", ttyname(fd));

        set_speed (fd, 19200);
        while (set_Parity (fd, 8, 1, 'N') == FALSE)
        {
            printf ("Set Parity Error\n");
            sleep(1);
        }
        printf("set_Parity %s successfully\n", ttyname(fd));

        char buf[255];
        memset(buf, 0 , sizeof(buf));

        fd_set rd;


        int read_len = 0;

        while(1)
        {
            FD_ZERO(&rd);
            FD_SET(fd, &rd);
            if(select(fd+1, &rd, NULL, NULL, NULL) < 0)
            {
                perror("select error\n");
                break;
            }
            else
            {
                if(FD_ISSET(fd, &rd))
                {
                    if((read_len = read(fd, buf, sizeof(buf))) > 0)
                    {
                        printf("read = %d:[%s]\n", read_len, buf);
                        if(read_len < 90 && read_len > 1){
                            post_data(buf);
                        }
                        memset(buf, 0 , sizeof(buf));
                    }else{
                        perror("read error\n");
                        break;
                    }
                }
            }
        }

        FD_CLR(fd, &rd);
        close (fd);
    }
}

int main ()
{
    daemon(0, 0);

    readTTY();
    return 0;
}
