# -------------------------------------------------
# Project created by QtCreator 2010-01-13T14:10:41
# -------------------------------------------------



QT += core gui declarative widgets serialport network
QT += webkit
QT += webkit webkitwidgets

CONFIG += qt
CONFIG -= static

TARGET = SerialChart
TEMPLATE = app
LIBS += -lsetupapi
SOURCES += main.cpp \
    mainwindow.cpp \
    portbase.cpp \
    configuration.cpp \
    chart.cpp \
    hiddevice.cpp \
    portrs232.cpp \
    porthid.cpp \
    decoderbase.cpp \
    decodercsv.cpp \
    displaybase.cpp \
    decoderhdlc.cpp \
    decoderbin.cpp \
    plugin.cpp \
    decoderplugin.cpp
HEADERS += mainwindow.h \
    common.h \
    portbase.h \
    configuration.h \
    chart.h \
    hiddevice.h \
    portrs232.h \
    porthid.h \
    decoderbase.h \
    decodercsv.h \
    displaybase.h \
    decoderhdlc.h \
    decoderbin.h \
    plugin.h \
    decoderplugin.h
FORMS += mainwindow.ui
RESOURCES += resources.qrc
OTHER_FILES += notes.txt

RC_FILE += \
    ea.rc

