#!/usr/bin/env python
# -*- coding: utf-8 -*- 

import email
import time
import MySQLdb
import sys
import os

param_conn=[]
db=os.path.dirname(os.path.realpath(__file__)) + "/.db"
with open(db) as f:
  param_conn = [x.strip() for x in f.readline().strip().split(':')]

if len(param_conn)!=4:
	print "Parametros de conexion a base de datos incorrectos"
	sys.exit ()

previo = 'por importe de'
post = 'EUR'

conn = MySQLdb.connect(*param_conn)

msg = email.message_from_file(sys.stdin)
signo = 0
print 'Procesando mensaje de ' + msg['From'] + " con asunto " + msg['Subject']
asunto = msg['Subject']
if 'alerta de Abono Superior' in asunto:
	signo = 1
elif 'alerta de Cargo Superior' in asunto:
	signo = -1

if signo == 0:
	print '"' + asunto + '" no es aun asunto a procesar'
	sys.exit ()

if msg.get_content_maintype() == 'multipart':
        for part in msg.get_payload ():
                if part.get_content_maintype () == 'text':
                    texto = part.get_payload ()
else:
        texto = msg.get_payload ()

	

ini = texto.find (previo)
if ini == -1:
        sys.exit ()
fin = texto.find (post, ini)
ini += len (previo)
valor = signo * float (texto[ini:fin].replace (',', '.'))
fecha = time.mktime (email.utils.parsedate (msg['Date']))        
consulta = 'insert into cuenta_triodos (fecha, valor) values (FROM_UNIXTIME(%i), %f)' % (fecha, valor)
with conn:
        cur = conn.cursor()
        cur.execute (consulta)

print 'Mensaje procesado e insertado'
