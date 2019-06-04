import psycopg2

# Conexao com o BD
con = psycopg2.connect(host='localhost', user='postgres', password='postgres', dbname='cda')

# Cria cursor para facilitar mexer no BD
c = con.cursor()

# Cria a tabela de Usuarios
c.execute("create table usuarios (usuario varchar(20), senha varchar(20), permissao int)")
con.commit()

# Cria a tabela de log de acoes dos usuarios
c.execute("create table historico (usuario varchar(20), date date, hora time(0), modificacao varchar(255))")
con.commit()

# Cria a tabela de membros
c.execute("create table membros (nome varchar(80), rfid bigint, id int, presenca int)")
con.commit()

# Cria o log da porta
c.execute("create table logsporta (nome varchar(80), rfid bigint, InOut int, dia varchar(5), mes varchar(5), ano varchar(5), hora varchar(5), min varchar(5), seg varchar(5))")
con.commit()

# Tabela de entradas diarias
c.execute("create table pontos (nome varchar(80), presenca int, dia varchar(5), mes varchar(5), ano varchar(5), diasem varchar(5), horain varchar(5), minin varchar(5), horaout varchar(5), minout varchar(5))")
con.commit()

# Tabela dos horarios
c.execute("create table horarios (id int, dia varchar(5), horain varchar(5), minin varchar(5), horaout varchar(5), minout varchar(5))")
con.commit()

con.close()
