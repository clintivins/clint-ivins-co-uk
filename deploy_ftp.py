import ftplib, ssl, sys

FTP_HOST = '92.205.174.96'
FTP_USER = 'admin@clint-ivins.co.uk'
FTP_PASS = 'Mandrake@1976'

print('Connecting to FTP server...')
ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

ftp = ftplib.FTP_TLS(context=ctx)
ftp.connect(FTP_HOST, timeout=60)
ftp.login(FTP_USER, FTP_PASS)
ftp.prot_p()

# Set passive mode
ftp.set_pasv(True)

for d in ['public_html', 'htdocs', 'httpdocs', 'www', 'web']:
    try:
        ftp.cwd(d)
        break
    except:
        pass

files_to_upload = [
    'index.html',
    'nomad-map.php'
]

for file in files_to_upload:
    with open(f"/Users/clint/Documents/clint-ivins-co-uk/{file}", 'rb') as f:
        ftp.storbinary(f'STOR {file}', f)
        print(f'Successfully uploaded {file}')

ftp.quit()
print('Deployment complete!')
